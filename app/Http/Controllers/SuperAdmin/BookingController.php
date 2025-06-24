<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Taxi;
use App\Models\City;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['client', 'driver', 'taxi.agency', 'pickupCity', 'destinationCity']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhere('pickup_location', 'like', "%{$search}%")
                    ->orWhere('booking_uuid', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($cq) use ($search) {
                        $cq->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par ville de départ
        if ($request->filled('pickup_city')) {
            $query->where('pickup_city_id', $request->pickup_city);
        }

        // Filtre par ville de destination
        if ($request->filled('destination_city')) {
            $query->where('destination_city_id', $request->destination_city);
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

        // Filtre par chauffeur assigné
        if ($request->filled('driver_status')) {
            if ($request->driver_status === 'assigned') {
                $query->whereNotNull('assigned_driver_id');
            } else {
                $query->whereNull('assigned_driver_id');
            }
        }

        $bookings = $query->latest('pickup_datetime')->paginate(10);

        // Statistiques globales
        $stats = $this->getGlobalStats();

        // Données pour les filtres
        $cities = City::orderBy('name')->get();

        return view('super-admin.bookings.index', compact('bookings', 'stats', 'cities'));
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'client',
            'driver',
            'taxi.agency',
            'pickupCity',
            'destinationCity',
            'applications.driver.taxi',
            'applications.taxi'
            
        ]);

        return view('super-admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $booking->load(['client', 'driver', 'taxi', 'pickupCity', 'destinationCity']);

        // Chauffeurs disponibles (avec taxi et dans la même ville de départ)
        $availableDrivers = User::whereHas('role', function ($q) {
            $q->where('name', 'DRIVER');
        })
            ->where('status', 'active')
            ->whereHas('taxi', function ($q) use ($booking) {
                $q->where('is_available', true)
                    ->where('city_id', $booking->pickup_city_id);
            })
            ->with('taxi')
            ->get();

        return view('super-admin.bookings.edit', compact('booking', 'availableDrivers'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:PENDING,ASSIGNED,IN_PROGRESS,COMPLETED,CANCELLED,NO_TAXI_FOUND',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'estimated_fare' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        // Si on assigne un chauffeur, récupérer son taxi
        if ($validated['assigned_driver_id']) {
            $driver = User::with('taxi')->find($validated['assigned_driver_id']);
            if ($driver && $driver->taxi) {
                $validated['assigned_taxi_id'] = $driver->taxi->id;

                // Marquer le taxi comme non disponible
                $driver->taxi->update(['is_available' => false]);
            }
        }

        // Si on change le statut vers COMPLETED, marquer le taxi comme disponible
        if ($validated['status'] === 'COMPLETED' && $booking->taxi) {
            $booking->taxi->update(['is_available' => true]);
        }

        // Si on annule, libérer le taxi
        if ($validated['status'] === 'CANCELLED' && $booking->taxi) {
            $booking->taxi->update(['is_available' => true]);
        }

        $booking->update($validated);

        return redirect()->route('super-admin.bookings.show', $booking)
            ->with('success', 'Réservation mise à jour avec succès!');
    }

    public function destroy(Booking $booking)
    {
        // Vérifier si la réservation peut être supprimée
        if (in_array($booking->status, ['IN_PROGRESS', 'ASSIGNED'])) {
            return back()->with('error', 'Impossible de supprimer une réservation en cours.');
        }

        // Libérer le taxi si assigné
        if ($booking->taxi) {
            $booking->taxi->update(['is_available' => true]);
        }

        $booking->delete();

        return redirect()->route('super-admin.bookings.index')
            ->with('success', 'Réservation supprimée avec succès!');
    }

    public function assignDriver(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:users,id',
        ]);

        $driver = User::with('taxi')->find($validated['driver_id']);

        if (!$driver || !$driver->taxi) {
            return back()->with('error', 'Chauffeur ou taxi non trouvé.');
        }

        $booking->update([
            'assigned_driver_id' => $driver->id,
            'assigned_taxi_id' => $driver->taxi->id,
            'status' => 'ASSIGNED'
        ]);

        // Marquer le taxi comme non disponible
        $driver->taxi->update(['is_available' => false]);

        return back()->with('success', 'Chauffeur assigné avec succès!');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:PENDING,ASSIGNED,IN_PROGRESS,COMPLETED,CANCELLED,NO_TAXI_FOUND',
        ]);

        $oldStatus = $booking->status;
        $booking->update($validated);

        // Gérer la disponibilité du taxi selon le statut
        if ($booking->taxi) {
            if ($validated['status'] === 'COMPLETED' || $validated['status'] === 'CANCELLED') {
                $booking->taxi->update(['is_available' => true]);
            } elseif ($validated['status'] === 'ASSIGNED' || $validated['status'] === 'IN_PROGRESS') {
                $booking->taxi->update(['is_available' => false]);
            }
        }

        return back()->with('success', "Statut changé de {$oldStatus} vers {$validated['status']}!");
    }

    private function getGlobalStats()
    {
        return [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'PENDING')->count(),
            'assigned_bookings' => Booking::where('status', 'ASSIGNED')->count(),
            'in_progress_bookings' => Booking::where('status', 'IN_PROGRESS')->count(),
            'completed_bookings' => Booking::where('status', 'COMPLETED')->count(),
            'cancelled_bookings' => Booking::where('status', 'CANCELLED')->count(),
            'no_taxi_found' => Booking::where('status', 'NO_TAXI_FOUND')->count(),
            'total_revenue' => Booking::where('status', 'COMPLETED')->sum('estimated_fare'),
            'revenue_today' => Booking::where('status', 'COMPLETED')
                ->whereDate('created_at', today())
                ->sum('estimated_fare'),
            'revenue_month' => Booking::where('status', 'COMPLETED')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('estimated_fare'),
            'bookings_today' => Booking::whereDate('created_at', today())->count(),
            'completion_rate' => $this->getCompletionRate(),
            'average_fare' => Booking::where('status', 'COMPLETED')->avg('estimated_fare'),
        ];
    }

    private function getCompletionRate()
    {
        $total = Booking::whereIn('status', ['COMPLETED', 'CANCELLED'])->count();
        $completed = Booking::where('status', 'COMPLETED')->count();

        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }
}
