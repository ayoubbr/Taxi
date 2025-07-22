<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Taxi;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TaxiController extends Controller
{
    /**
     * Affiche la liste des taxis de l'agence.
     */
    public function index(Request $request)
    {
        $agency = Auth::user()->agency;

        $query = $agency->taxis()->with(['city', 'driver']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('availability')) {
            $isAvailable = $request->availability === 'available';
            $query->where('is_available', $isAvailable);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        $taxis = $query->latest()->paginate(12);
        $cities = City::orderBy('name')->get();

        return view('agency.taxis.index', compact('taxis', 'cities'));
    }

    /**
     * Affiche le formulaire pour créer un nouveau taxi.
     */
    public function create()
    {
        $agency = Auth::user()->agency;

        // Chauffeurs disponibles (sans taxi assigné et actifs)
        $availableDrivers = $agency->drivers()
            ->where('status', 'active')
            ->whereDoesntHave('taxi')
            ->orderBy('firstname')
            ->get();

        $cities = City::orderBy('name')->get();

        return view('agency.taxis.create', compact('availableDrivers', 'cities'));
    }

    /**
     * Enregistre un nouveau taxi.
     */
    public function store(Request $request)
    {
        $agency = Auth::user()->agency;

        $validatedData = $request->validate([
            'license_plate' => [
                'required',
                'string',
                'max:50',
                'unique:taxis',
                'regex:/^[A-Z0-9\-]+$/'
            ],
            'model' => 'required|string|max:100',
            'type' => 'required|in:standard,van,luxe',
            'city_id' => 'required|exists:cities,id',
            'capacity' => 'required|integer|min:1|max:9',
            'driver_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($agency) {
                    if ($value) {
                        $driver = User::find($value);
                        if (!$driver || $driver->agency_id !== $agency->id) {
                            $fail('Le chauffeur sélectionné n\'appartient pas à votre agence.');
                        }
                        if ($driver->assignedTaxi) {
                            $fail('Le chauffeur sélectionné a déjà un taxi assigné.');
                        }
                    }
                }
            ],
            'is_available' => 'required|boolean',
        ], [
            'license_plate.regex' => 'La plaque d\'immatriculation ne peut contenir que des lettres, chiffres et tirets.',
            'license_plate.unique' => 'Cette plaque d\'immatriculation existe déjà.',
        ]);

        try {
            DB::beginTransaction();

            $taxi = new Taxi($validatedData);
            $taxi->agency_id = $agency->id;
            $taxi->save();

            // Assigner le chauffeur si spécifié
            if ($request->driver_id) {
                $taxi->update(['driver_id' => $request->driver_id]);
            }

            DB::commit();

            return redirect()
                ->route('agency.taxis.index')
                ->with('success', 'Taxi créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du taxi.');
        }
    }

    /**
     * Affiche les détails d'un taxi.
     */
    public function show(Taxi $taxi)
    {
        // Vérifier que le taxi appartient à l'agence
        if ($taxi->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation d\'accéder à ce taxi.');
        }

        // Charger les relations
        $taxi->load(['city', 'driver', 'agency']);

        // Récupérer les courses récentes
        $recentBookings = $taxi->bookings()
            ->with(['client', 'driver'])
            ->latest()
            ->limit(10)
            ->get();

        // Calculer les statistiques
        $totalBookings = $taxi->bookings()->count();
        $completedBookings = $taxi->bookings()->where('status', 'COMPLETED')->count();
        $cancelledBookings = $taxi->bookings()->where('status', 'CANCELLED')->count();
        $inProgressBookings = $taxi->bookings()->whereIn('status', ['PENDING', 'CONFIRMED', 'IN_PROGRESS'])->count();

        $totalRevenue = $taxi->bookings()->where('status', 'COMPLETED')->sum('estimated_fare');
        $thisMonthBookings = $taxi->bookings()->whereMonth('created_at', now()->month)->count();
        $thisMonthRevenue = $taxi->bookings()
            ->where('status', 'COMPLETED')
            ->whereMonth('created_at', now()->month)
            ->sum('estimated_fare');

        // Calculs des taux et moyennes
        $completionRate = $totalBookings > 0 ? ($completedBookings / $totalBookings) * 100 : 0;
        $cancellationRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;
        $averageFare = $completedBookings > 0 ? $totalRevenue / $completedBookings : 0;

        // Statistiques simulées (à implémenter avec de vraies données)
        $averageDuration = 25; // minutes
        $totalDistance = $completedBookings * 8; // km approximatif

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
            'average_duration' => $averageDuration,
            'total_distance' => $totalDistance,
        ];

        return view('agency.taxis.show', compact('taxi', 'stats', 'recentBookings'));
    }

    /**
     * Affiche le formulaire pour modifier un taxi.
     */
    public function edit(Taxi $taxi)
    {
        // Vérifier que le taxi appartient à l'agence
        if ($taxi->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier ce taxi.');
        }

        $agency = Auth::user()->agency;

        // Chauffeurs disponibles + le chauffeur actuellement assigné
        $availableDrivers = $agency->drivers()
            ->where('status', 'active')
            ->where(function ($query) use ($taxi) {
                $query->whereDoesntHave('taxi')
                    ->orWhere('id', $taxi->driver_id);
            })
            ->orderBy('firstname')
            ->get();

        $cities = City::orderBy('name')->get();

        return view('agency.taxis.edit', compact('taxi', 'availableDrivers', 'cities'));
    }

    /**
     * Met à jour les informations d'un taxi.
     */
    public function update(Request $request, Taxi $taxi)
    {
        // Vérifier que le taxi appartient à l'agence
        if ($taxi->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier ce taxi.');
        }

        $agency = Auth::user()->agency;

        $validatedData = $request->validate([
            'license_plate' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('taxis')->ignore($taxi->id)
            ],
            'model' => 'required|string|max:100',
            'type' => 'required|in:standard,van,luxe',
            'city_id' => 'required|exists:cities,id',
            'capacity' => 'required|integer|min:1|max:9',
            'driver_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($agency, $taxi) {
                    if ($value) {
                        $driver = User::find($value);
                        if (!$driver || $driver->agency_id !== $agency->id) {
                            $fail('Le chauffeur sélectionné n\'appartient pas à votre agence.');
                        }
                        // Vérifier que le chauffeur n'a pas déjà un autre taxi
                        $existingTaxi = Taxi::where('driver_id', $value)->where('id', '!=', $taxi->id)->first();
                        if ($existingTaxi) {
                            $fail('Le chauffeur sélectionné a déjà un autre taxi assigné.');
                        }
                    }
                }
            ],
            'is_available' => 'required|boolean',
        ], [
            'license_plate.regex' => 'La plaque d\'immatriculation ne peut contenir que des lettres, chiffres et tirets.',
        ]);

        try {
            DB::beginTransaction();

            $oldDriverId = $taxi->driver_id;
            $newDriverId = $request->driver_id;

            // Mettre à jour le taxi
            $taxi->update($validatedData);

            // Gérer le changement de chauffeur
            if ($oldDriverId !== $newDriverId) {
                // Si on retire le chauffeur, marquer le taxi comme disponible
                if (!$newDriverId) {
                    $taxi->update(['is_available' => true]);
                }
                // Si on assigne un nouveau chauffeur, marquer comme occupé
                elseif ($newDriverId && $taxi->is_available) {
                    $taxi->update(['is_available' => false]);
                }
            }

            DB::commit();

            $message = 'Taxi mis à jour avec succès.';
            if ($oldDriverId !== $newDriverId) {
                if ($newDriverId) {
                    $driver = User::find($newDriverId);
                    $message .= " Chauffeur {$driver->firstname} {$driver->lastname} assigné.";
                } elseif ($oldDriverId) {
                    $message .= " Attribution de chauffeur supprimée.";
                }
            }

            return redirect()
                ->route('agency.taxis.show', $taxi)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du taxi.');
        }
    }

    /**
     * Bascule la disponibilité d'un taxi.
     */
    public function toggleAvailability(Taxi $taxi)
    {
        // Vérifier que le taxi appartient à l'agence
        if ($taxi->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier ce taxi.');
        }

        try {
            DB::beginTransaction();

            $newAvailability = !$taxi->is_available;

            // Si on marque comme occupé et qu'il y a des courses en cours, empêcher l'action
            if (!$newAvailability) {
                $activeBookings = $taxi->bookings()
                    ->whereIn('status', ['PENDING', 'CONFIRMED', 'IN_PROGRESS'])
                    ->count();

                if ($activeBookings > 0) {
                    return redirect()
                        ->back()
                        ->with('error', 'Impossible de marquer ce taxi comme occupé car il a des courses en cours.');
                }
            }

            $taxi->update(['is_available' => $newAvailability]);

            DB::commit();

            $statusText = $newAvailability ? 'marqué comme disponible' : 'marqué comme occupé';

            return redirect()
                ->back()
                ->with('success', "Taxi {$statusText} avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors du changement de disponibilité.');
        }
    }


    public function destroy(Taxi $taxi)
    {
        // Vérifier que le taxi appartient à l'agence
        if ($taxi->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de supprimer ce taxi.');
        }

        try {
            DB::beginTransaction();

            // Vérifier qu'il n'y a pas de courses en cours
            $activeBookings = $taxi->bookings()
                ->whereIn('status', ['PENDING', 'CONFIRMED', 'IN_PROGRESS'])
                ->count();

            if ($activeBookings > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Impossible de supprimer ce taxi car il a des courses en cours.');
            }

            $licensePlate = $taxi->license_plate;
            $taxi->delete();

            DB::commit();

            return redirect()
                ->route('agency.taxis.index')
                ->with('success', "Taxi {$licensePlate} supprimé avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la suppression du taxi.');
        }
    }
}
