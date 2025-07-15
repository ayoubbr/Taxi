<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Taxi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class DriverController extends Controller
{

    public function index(Request $request)
    {
        $agency = Auth::user()->agency;
        $driverRoleId = Role::where('name', 'DRIVER')->value('id');

        $query = $agency->users()->where('role_id', $driverRoleId);

        // --- Application des filtres ---

        // Filtre de recherche globale (nom, prénom, username, email)
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('firstname', 'like', $searchTerm)
                    ->orWhere('lastname', 'like', $searchTerm)
                    ->orWhere('username', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtre par date d'ajout
        if ($request->filled('creation_date')) {
            $query->whereDate('created_at', $request->input('creation_date'));
        }

        // --- Fin des filtres ---
        $drivers = $query->latest()->paginate(9)->appends($request->query());

        return view('agency.drivers.index', compact('drivers'));
    }

    public function create()
    {
        $availableTaxis   = Taxi::where('is_available', true)->whereDoesntHave('driver')->get();
        return view('agency.drivers.create', compact('availableTaxis'));
    }

    public function store(Request $request)
    {
        $agency = Auth::user()->agency;
        $driverRole = Role::where('name', 'DRIVER')->first();

        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'username' => 'required|string|max:100|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agency_id' => $agency->id,
            'role_id' => $driverRole->id,
            'status' => $request->status // Ou 'inactive' par défaut si vous voulez une activation manuelle
        ]);

        $taxi = Taxi::find($request->taxi_id);

        if ($taxi && $user) {
            $taxi->update(['driver_id' => $user->id]);
        }

        return redirect()->route('agency.drivers.index')->with('success', 'Chauffeur créé avec succès.');
    }

    public function edit(User $driver)
    {
        // Sécurité : Vérifier que le chauffeur appartient bien à l'agence de l'admin
        if ($driver->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation d\'accéder à ce chauffeur.');
        }

        // Vérifier que l'utilisateur est bien un chauffeur
        if (!$driver->hasRole('DRIVER')) {
            abort(404, 'Chauffeur non trouvé.');
        }

        // Récupérer les taxis disponibles (sans chauffeur) + le taxi actuel du chauffeur
        $availableTaxis = Taxi::where('agency_id', Auth::user()->agency_id)
            ->where(function ($query) use ($driver) {
                $query->where('is_available', true)
                    ->whereNull('driver_id')
                    ->orWhere('driver_id', $driver->id);
            })
            ->orderBy('license_plate')
            ->get();

        return view('agency.drivers.edit', compact('driver', 'availableTaxis'));
    }

    public function update(Request $request, User $driver)
    {
        // Sécurité : Vérifier que le chauffeur appartient bien à l'agence de l'admin
        if ($driver->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier ce chauffeur.');
        }

        // Vérifier que l'utilisateur est bien un chauffeur
        if (!$driver->hasRole('DRIVER')) {
            abort(404, 'Chauffeur non trouvé.');
        }

        // Validation des données
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:100|regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
            'lastname' => 'required|string|max:100|regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
            'username' => [
                'required',
                'string',
                'max:100',
                'min:3',
                'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('users')->ignore($driver->id)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users')->ignore($driver->id)
            ],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'status' => 'required|in:active,inactive,suspended',
            'taxi_id' => [
                'nullable',
                'exists:taxis,id',
                function ($attribute, $value, $fail) use ($driver) {
                    if ($value) {
                        $taxi = Taxi::find($value);
                        // Vérifier que le taxi appartient à la même agence
                        if ($taxi && $taxi->agency_id !== Auth::user()->agency_id) {
                            $fail('Le taxi sélectionné n\'appartient pas à votre agence.');
                        }
                        // Vérifier que le taxi est disponible ou assigné au chauffeur actuel
                        if ($taxi && $taxi->driver_id && $taxi->driver_id !== $driver->id) {
                            $fail('Le taxi sélectionné est déjà assigné à un autre chauffeur.');
                        }
                    }
                }
            ],
        ], [
            'firstname.regex' => 'Le prénom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'lastname.regex' => 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'username.regex' => 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres, points, tirets et underscores.',
            'username.min' => 'Le nom d\'utilisateur doit contenir au moins 3 caractères.',
        ]);

        try {
            DB::beginTransaction();

            // Sauvegarder l'ancien taxi_id pour la gestion des changements
            $oldTaxiId = null;
            if ($driver->taxi) {
                $oldTaxiId = $driver->taxi->id;
            }
            $newTaxiId = $request->taxi_id;

            // Mettre à jour les informations du chauffeur
            $updateData = [
                'firstname' => $validatedData['firstname'],
                'lastname' => $validatedData['lastname'],
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'status' => $validatedData['status'],
            ];


            // Mettre à jour le mot de passe si fourni
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validatedData['password']);
            }

            $driver->update($updateData);

            // Gestion de l'attribution des taxis
            $this->handleTaxiAssignment($driver, $oldTaxiId, $newTaxiId);

            DB::commit();

            // Message de succès personnalisé selon les changements
            $message = 'Chauffeur mis à jour avec succès.';
            if ($oldTaxiId !== $newTaxiId) {
                if ($newTaxiId) {
                    $taxi = Taxi::find($newTaxiId);
                    $message .= " Taxi {$taxi->license_plate} assigné.";
                } elseif ($oldTaxiId) {
                    $message .= " Attribution de taxi supprimée.";
                }
            }

            return redirect()
                ->back()
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du chauffeur. Veuillez réessayer.');
        }
    }

    private function handleTaxiAssignment(User $driver, $oldTaxiId, $newTaxiId)
    {
        // Si l'attribution n'a pas changé, ne rien faire
        if ($oldTaxiId == $newTaxiId) {
            return;
        }

        // Libérer l'ancien taxi si il y en avait un
        if ($oldTaxiId) {
            $oldTaxi = Taxi::find($oldTaxiId);
            if ($oldTaxi) {
                $oldTaxi->update([
                    'driver_id' => null
                ]);
            }
        }

        // Assigner le nouveau taxi si spécifié
        if ($newTaxiId) {
            $newTaxi = Taxi::find($newTaxiId);
            if ($newTaxi && $newTaxi->agency_id === Auth::user()->agency_id) {
                $newTaxi->update([
                    'driver_id' => $driver->id
                ]);
            }
        }
    }

    public function show(User $driver)
    {
        // Sécurité : Vérifier que le chauffeur appartient bien à l'agence de l'admin
        if ($driver->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation d\'accéder à ce chauffeur.');
        }

        // Vérifier que l'utilisateur est bien un chauffeur
        if (!$driver->hasRole('driver')) {
            abort(404, 'Chauffeur non trouvé.');
        }

        // Charger les relations nécessaires
        $driver->load(['agency']);

        // Récupérer le taxi assigné (relation inverse)
        $driver->taxi = Taxi::where('driver_id', $driver->id)->first();

        // Récupérer les courses récentes
        $recentBookings = $driver->assignedDriverBookings()
            ->with(['client'])
            ->latest()
            ->limit(10)
            ->get();

        // Calculer les statistiques du chauffeur
        $totalBookings = $driver->assignedDriverBookings()->count();
        $completedBookings = $driver->assignedDriverBookings()->where('status', 'COMPLETED')->count();
        $cancelledBookings = $driver->assignedDriverBookings()->where('status', 'CANCELLED')->count();
        $inProgressBookings = $driver->assignedDriverBookings()->whereIn('status', ['PENDING', 'CONFIRMED', 'IN_PROGRESS'])->count();

        $totalRevenue = $driver->assignedDriverBookings()->where('status', 'COMPLETED')->sum('estimated_fare');
        $thisMonthBookings = $driver->assignedDriverBookings()->whereMonth('created_at', now()->month)->count();
        $thisMonthRevenue = $driver->assignedDriverBookings()
            ->where('status', 'COMPLETED')
            ->whereMonth('created_at', now()->month)
            ->sum('estimated_fare');

        // Calculs des taux
        $completionRate = $totalBookings > 0 ? ($completedBookings / $totalBookings) * 100 : 0;
        $cancellationRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;
        $averageFare = $completedBookings > 0 ? $totalRevenue / $completedBookings : 0;

        // Note moyenne (simulée pour l'instant - à implémenter avec un système de notation)
        $averageRating = 4.2; // Valeur par défaut
        $totalRatings = $completedBookings; // Simulé

        $stats = [
            'total_bookings' => $totalBookings,
            'completed_bookings' => $completedBookings,
            'cancelled_bookings' => $cancelledBookings,
            'in_progress_bookings' => $inProgressBookings,
            'total_revenue' => $totalRevenue,
            'this_month_bookings' => $thisMonthBookings,
            'this_month_revenue' => $thisMonthRevenue,
            'completion_rate' => $completionRate,
            'cancellation_rate' => $cancellationRate,
            'average_fare' => $averageFare,
            'average_rating' => $averageRating,
            'total_ratings' => $totalRatings,
        ];

        return view('agency.drivers.show', compact('driver', 'stats', 'recentBookings'));
    }


    public function destroy(User $driver)
    {
        // Sécurité : Vérifier que le chauffeur appartient bien à l'agence de l'admin
        if ($driver->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de supprimer ce chauffeur.');
        }

        // Vérifier que l'utilisateur est bien un chauffeur
        if (!$driver->hasRole('DRIVER')) {
            abort(404, 'Chauffeur non trouvé.');
        }

        try {
            DB::beginTransaction();

            // Vérifier qu'il n'y a pas de courses en cours
            $activeBookings = $driver->assignedDriverBookings()
                ->whereIn('status', ['PENDING', 'CONFIRMED', 'IN_PROGRESS'])
                ->count();

            if ($activeBookings > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Impossible de supprimer ce chauffeur car il a des courses en cours.');
            }

            // Libérer le taxi si assigné
            if ($driver->taxi) {
                $driver->taxi->update([
                    'driver_id' => null,
                    'is_available' => true
                ]);
            }

            $driverName = $driver->firstname . ' ' . $driver->lastname;
            $driver->delete();

            DB::commit();

            return redirect()
                ->route('agency.drivers.index')
                ->with('success', "Chauffeur {$driverName} supprimé avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la suppression du chauffeur.');
        }
    }


    public function toggleStatus(User $driver)
    {
        // Sécurité : Vérifier que l'admin modifie un chauffeur de sa propre agence
        if ($driver->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Action non autorisée.');
        }

        // Déterminer le nouveau statut
        $newStatus = $driver->status === 'active' ? 'inactive' : 'active';

        $driver->update(['status' => $newStatus]);

        $message = "Le statut du chauffeur a été mis à jour à '{$newStatus}'.";

        return redirect()->back()->with('success', $message);
    }

    // public function toggleStatus(User $driver)
    // {
    //     // Sécurité : Vérifier que le chauffeur appartient bien à l'agence de l'admin
    //     if ($driver->agency_id !== Auth::user()->agency_id) {
    //         abort(403, 'Vous n\'avez pas l\'autorisation de modifier ce chauffeur.');
    //     }

    //     // Vérifier que l'utilisateur est bien un chauffeur
    //     if (!$driver->hasRole('driver')) {
    //         abort(404, 'Chauffeur non trouvé.');
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $newStatus = $driver->status === 'active' ? 'inactive' : 'active';

    //         // Si on désactive le chauffeur et qu'il a des courses en cours, empêcher l'action
    //         if ($newStatus === 'inactive') {
    //             $activeBookings = $driver->assignedDriverBookings()
    //                 ->whereIn('status', ['PENDING', 'CONFIRMED', 'IN_PROGRESS'])
    //                 ->count();

    //             if ($activeBookings > 0) {
    //                 return redirect()
    //                     ->back()
    //                     ->with('error', 'Impossible de désactiver ce chauffeur car il a des courses en cours.');
    //             }
    //         }

    //         $driver->update(['status' => $newStatus]);

    //         DB::commit();

    //         $statusText = $newStatus === 'active' ? 'activé' : 'désactivé';

    //         return redirect()
    //             ->back()
    //             ->with('success', "Chauffeur {$statusText} avec succès.");
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return redirect()
    //             ->back()
    //             ->with('error', 'Une erreur est survenue lors du changement de statut.');
    //     }
    // }


    //     public function show(User $driver)
    //     {
    //         // Sécurité : Vérifier que le chauffeur appartient bien à l'agence de l'admin
    //         if ($driver->agency_id !== Auth::user()->agency_id) {
    //             abort(403, 'Vous n\'avez pas l\'autorisation d\'accéder à ce chauffeur.');
    //         }

    //         // Vérifier que l'utilisateur est bien un chauffeur
    //         if (!$driver->hasRole('driver')) {
    //             abort(404, 'Chauffeur non trouvé.');
    //         }

    //         // Charger les relations nécessaires
    //         $driver->load(['taxi', 'assignedDriverBookings' => function ($query) {
    //             $query->latest()->limit(10);
    //         }]);

    //         // Statistiques du chauffeur
    //         $stats = [
    //             'total_bookings' => $driver->assignedDriverBookings()->count(),
    //             'completed_bookings' => $driver->assignedDriverBookings()->where('status', 'COMPLETED')->count(),
    //             'in_progress_bookings' => $driver->assignedDriverBookings()->whereIn('status', ['PENDING', 'CONFIRMED', 'IN_PROGRESS'])->count(),
    //             'total_revenue' => $driver->assignedDriverBookings()->where('status', 'COMPLETED')->sum('estimated_fare'),
    //             'this_month_bookings' => $driver->assignedDriverBookings()->whereMonth('created_at', now()->month)->count(),
    //             'this_month_revenue' => $driver->assignedDriverBookings()
    //                 ->where('status', 'COMPLETED')
    //                 ->whereMonth('created_at', now()->month)
    //                 ->sum('estimated_fare'),
    //         ];

    //         return view('agency-admin.drivers.show', compact('driver', 'stats'));
    //     }

    //     
    //     public function store(Request $request)
    //     {
    //         // Validation des données
    //         $validatedData = $request->validate([
    //             'firstname' => 'required|string|max:100|regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
    //             'lastname' => 'required|string|max:100|regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
    //             'username' => [
    //                 'required',
    //                 'string',
    //                 'max:100',
    //                 'min:3',
    //                 'regex:/^[a-zA-Z0-9._-]+$/',
    //                 'unique:users'
    //             ],
    //             'email' => 'required|string|email|max:100|unique:users',
    //             'password' => ['required', 'confirmed', Password::min(8)],
    //             'status' => 'required|in:active,inactive',
    //             'taxi_id' => [
    //                 'nullable',
    //                 'exists:taxis,id',
    //                 function ($attribute, $value, $fail) {
    //                     if ($value) {
    //                         $taxi = Taxi::find($value);
    //                         // Vérifier que le taxi appartient à la même agence
    //                         if ($taxi && $taxi->agency_id !== Auth::user()->agency_id) {
    //                             $fail('Le taxi sélectionné n\'appartient pas à votre agence.');
    //                         }
    //                         // Vérifier que le taxi est disponible
    //                         if ($taxi && $taxi->driver_id) {
    //                             $fail('Le taxi sélectionné est déjà assigné à un autre chauffeur.');
    //                         }
    //                     }
    //                 }
    //             ],
    //         ], [
    //             'firstname.regex' => 'Le prénom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
    //             'lastname.regex' => 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
    //             'username.regex' => 'Le nom d\'utilisateur ne peut contenir que des lettres, chiffres, points, tirets et underscores.',
    //             'username.min' => 'Le nom d\'utilisateur doit contenir au moins 3 caractères.',
    //         ]);

    //         try {
    //             DB::beginTransaction();

    //             // Créer le chauffeur
    //             $driver = User::create([
    //                 'firstname' => $validatedData['firstname'],
    //                 'lastname' => $validatedData['lastname'],
    //                 'username' => $validatedData['username'],
    //                 'email' => $validatedData['email'],
    //                 'password' => Hash::make($validatedData['password']),
    //                 'status' => $validatedData['status'],
    //                 'agency_id' => Auth::user()->agency_id,
    //                 'taxi_id' => $request->taxi_id,
    //             ]);

    //             // Assigner le rôle de chauffeur
    //             $driver->assignRole('driver');

    //             // Assigner le taxi si spécifié
    //             if ($request->taxi_id) {
    //                 $taxi = Taxi::find($request->taxi_id);
    //                 if ($taxi && $taxi->agency_id === Auth::user()->agency_id) {
    //                     $taxi->update([
    //                         'driver_id' => $driver->id,
    //                         'is_available' => false
    //                     ]);
    //                 }
    //             }

    //             DB::commit();

    //             return redirect()
    //                 ->route('agency-admin.drivers.index')
    //                 ->with('success', 'Chauffeur créé avec succès.');

    //         } catch (\Exception $e) {
    //             DB::rollBack();

    //             return redirect()
    //                 ->back()
    //                 ->withInput()
    //                 ->with('error', 'Une erreur est survenue lors de la création du chauffeur. Veuillez réessayer.');
    //         }
    //     }
}
