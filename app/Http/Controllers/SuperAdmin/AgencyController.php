<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgencyController extends Controller
{
    public function index(Request $request)
    {
        $query = Agency::withCount([
            'users',
            'drivers' => function ($query) {
                $query->whereHas('role', function ($q) {
                    $q->where('name', 'DRIVER');
                });
            },
            'taxis',
            'bookings'
        ]);


        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $agencies = $query->latest()->paginate(8);

        return view('super-admin.agencies.index', compact('agencies'));
    }

    public function show(Agency $agency)
    {
        // Charger les relations avec les bonnes contraintes
        $agency->load([
            'users' => function ($query) {
                $query->latest()->take(10);
            },
            'taxis' => function ($query) {
                $query->with('driver')->latest()->take(10);
            },
            'bookings' => function ($query) {
                $query->with(['client', 'driver', 'taxi'])->latest()->take(10);
            }
        ]);

        // Utiliser la méthode getStats() du modèle
        $stats = $agency->getStats();
        // Calculer les statistiques
        $stats = [
            'total_users' => $agency->users()->count(),
            'total_drivers' => $agency->users()->whereHas('role', function ($q) {
                $q->where('name', 'DRIVER');
            })->count(),
            'active_drivers' => $agency->users()->whereHas('role', function ($q) {
                $q->where('name', 'DRIVER');
            })->where('status', 'active')->count(),
            'total_taxis' => $agency->taxis()->count(),
            'available_taxis' => $agency->taxis()->where('is_available', true)->count(),
            'total_bookings' => $agency->bookings()->count(),
            'assigned_bookings' => $agency->bookings()->where('status', 'ASSIGNED')->orWhere('status', 'IN_PROGRESS')->count(),
            'completed_bookings' => $agency->bookings()->where('status', 'COMPLETED')->count(),
            'cancelled_bookings' => $agency->bookings()->where('status', 'CANCELLED')->count(),
            'revenue_total' => $agency->bookings()->where('status', 'COMPLETED')->sum('estimated_fare'),
            'revenue_month' => $agency->bookings()
                ->where('status', 'COMPLETED')
                // ->whereMonth('created_at', now()->month)
                // ->whereYear('created_at', now()->year)
                ->sum('estimated_fare'),
            'revenue_today' => $agency->bookings()
                ->where('status', 'COMPLETED')
                // ->whereDate('created_at', today())
                ->sum('estimated_fare'),
        ];

        return view('super-admin.agencies.show', compact('agency', 'stats'));
    }

    public function create()
    {
        return view('super-admin.agencies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspendu',
        ]);

        // Gérer l'upload du logo
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('agencies/logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $agency = Agency::create($validated);

        return redirect()->route('super-admin.agencies.index')
            ->with('success', 'Agence créée avec succès!');
    }

    public function edit(Agency $agency)
    {
        return view('super-admin.agencies.edit', compact('agency'));
    }

    public function update(Request $request, Agency $agency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $agency->update($validated);

        return redirect()->route('super-admin.agencies.show', $agency)
            ->with('success', 'Agence mise à jour avec succès!');
    }

    public function destroy(Agency $agency)
    {
        // Vérifier s'il y a des chauffeurs actifs
        if ($agency->drivers()->where('is_active', true)->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une agence avec des chauffeurs actifs.');
        }

        // Vérifier s'il y a des réservations en cours
        if ($agency->bookings()->whereIn('status', ['PENDING', 'ASSIGNED', 'IN_PROGRESS'])->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une agence avec des réservations en cours.');
        }

        $agency->delete();

        return redirect()->route('super-admin.agencies.index')
            ->with('success', 'Agence supprimée avec succès!');
    }

    public function toggleStatus(Agency $agency)
    {
        $newStatus = $agency->status === 'active' ? 'inactive' : 'active';

        $agency->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Agence activée' : 'Agence désactivée';

        return back()->with('success', $message . ' avec succès!');
    }

    public function suspend(Agency $agency)
    {
        $agency->update(['status' => 'suspendu']);
        return back()->with('success', 'Agence suspendu avec succès!');
    }

    // public function users(Agency $agency)
    // {
    //     $users = $agency->users();
    //     return view('super-admin.agencies.users', compact('users'));
    // }

    // public function taxis(Agency $agency)
    // {
    //     $taxis = $agency->taxis();
    //     return view('super-admin.agencies.taxis', compact('taxis'));
    // }

    // public function bookings(Agency $agency)
    // {
    //     $bookings = $agency->bookings();
    //     return view('super-admin.agencies.bookings', compact('bookings'));
    // }

    // NOUVELLES MÉTHODES OPTIMISÉES
    public function users(Request $request, Agency $agency)
    {
        $query = $agency->users()->with('role');

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filtre par rôle
        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15);

        // Statistiques pour la vue
        $stats = [
            'total_users' => $agency->users()->count(),
            'active_users' => $agency->users()->where('status', 'active')->count(),
            'inactive_users' => $agency->users()->where('status', 'inactive')->count(),
            'drivers_count' => $agency->users()->whereHas('role', function ($q) {
                $q->where('name', 'DRIVER');
            })->count(),
            'clients_count' => $agency->users()->whereHas('role', function ($q) {
                $q->where('name', 'CLIENT');
            })->count(),
        ];

        return view('super-admin.agencies.users', compact('agency', 'users', 'stats'));
    }

    public function taxis(Request $request, Agency $agency)
    {
        $query = $agency->taxis()->with(['driver', 'city']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhereHas('driver', function ($dq) use ($search) {
                        $dq->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%");
                    });
            });
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par disponibilité
        if ($request->filled('availability')) {
            $isAvailable = $request->availability === 'true' ? 1  : 0;
            $query->where('is_available', $isAvailable);
        }

        // // Filtre par statut chauffeur
        // if ($request->filled('driver_status')) {
        //     if ($request->driver_status === 'assigned') {
        //         $query->whereNotNull('driver_id');
        //     } else {
        //         $query->whereNull('driver_id');
        //     }
        // }

        $taxis = $query->latest()->paginate(15);

        // Statistiques pour la vue
        $stats = [
            'total_taxis' => $agency->taxis()->count(),
            'available_taxis' => $agency->taxis()->where('is_available', true)->count(),
            'unavailable_taxis' => $agency->taxis()->where('is_available', false)->count(),
            'assigned_taxis' => $agency->taxis()->whereNotNull('driver_id')->count(),
            'unassigned_taxis' => $agency->taxis()->whereNull('driver_id')->count(),
            'standard_taxis' => $agency->taxis()->where('type', 'standard')->count(),
            'van_taxis' => $agency->taxis()->where('type', 'van')->count(),
            'luxe_taxis' => $agency->taxis()->where('type', 'luxe')->count(),
        ];

        return view('super-admin.agencies.taxis', compact('agency', 'taxis', 'stats'));
    }

    public function bookings(Request $request, Agency $agency)
    {
        $query = $agency->bookings()->with(['client', 'driver', 'taxi']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhere('pickup_location', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%")
                    ->orWhere('booking_uuid', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%");
                    });
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par type de taxi
        if ($request->filled('taxi_type')) {
            $query->where('taxi_type', $request->taxi_type);
        }

        // Filtre par date
        if ($request->filled('date_from')) {
            $query->whereDate('pickup_datetime', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('pickup_datetime', '<=', $request->date_to);
        }

        // Tri par défaut : plus récent en premier
        $query->latest('pickup_datetime');

        $bookings = $query->paginate(15);

        // Statistiques pour la vue
        $stats = [
            'total_bookings' => $agency->bookings()->count(),
            // 'pending_bookings' => $agency->bookings()->where('status', 'PENDING')->count(),
            'assigned' => $agency->bookings()->where('status', 'ASSIGNED')->count(),
            'in_progress' => $agency->bookings()->where('status', 'IN_PROGRESS')->count(),
            'completed_bookings' => $agency->bookings()->where('status', 'COMPLETED')->count(),
            'cancelled' => $agency->bookings()->where('status', 'CANCELLED')->count(),
            'no_taxi_found' => $agency->bookings()->where('status', 'NO_TAXI_FOUND')->count(),
            'total_revenue' => $agency->bookings()->where('status', 'COMPLETED')->sum('estimated_fare'),
            'revenue_month' => $agency->bookings()
                ->where('status', 'COMPLETED')
                // ->whereMonth('created_at', now()->month)
                ->sum('estimated_fare'),
        ];

        return view('super-admin.agencies.bookings', compact('agency', 'bookings', 'stats'));
    }
}
