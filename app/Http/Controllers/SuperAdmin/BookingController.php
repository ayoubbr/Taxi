<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\City;
use App\Notifications\BookingAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            ->orWhere('id', $booking->assigned_driver_id)
            ->with('taxi')
            ->get();

        $cities = City::all();

        return view('super-admin.bookings.edit', compact('booking', 'availableDrivers', 'cities'));
    }

    public function update(Request $request, Booking $booking)
    {
        try {
            $request->validate([
                'client_name' => 'required|string|max:255',
                'pickup_location' => 'required|string|max:500',
                'destination_city_id' => 'required',
                'pickup_datetime' => 'required|date|after:now',
                'taxi_type' => 'required|in:standard,luxe,van',
                'status' => 'required|in:PENDING,ASSIGNED,IN_PROGRESS,COMPLETED,CANCELLED,NO_TAXI_FOUND',
                'assigned_driver_id' => 'nullable|exists:users,id',
                'estimated_fare' => 'nullable|numeric|min:0',
            ]);

            DB::beginTransaction();


            // Store original values for comparison
            $originalStatus = $booking->status;
            $originalDriverId = $booking->assigned_driver_id;

            // If status is changed to PENDING, clear driver and taxi assignment
            $assignedDriverId = $request->status === 'PENDING' ? null : $request->assigned_driver_id;
            $assignedTaxiId = null;

            // Make previously assigned taxi available again if status is being set to PENDING
            if ($request->status === 'PENDING') {
                if ($booking->assigned_taxi_id) {
                    $previousTaxi = \App\Models\Taxi::find($booking->assigned_taxi_id);
                    if ($previousTaxi) {
                        $previousTaxi->is_available = true;
                        $previousTaxi->save();
                    }
                }
            } else {
                // If assigning a driver, find the taxi of that driver
                if ($request->assigned_driver_id) {
                    $driver = \App\Models\User::find($request->assigned_driver_id);
                    $assignedTaxiId = $driver?->taxi?->id;
                }
            }

            // Update booking
            $booking->update([
                'client_name' => $request->client_name,
                'pickup_location' => $request->pickup_location,
                'destination_city_id' => $request->destination_city_id,
                'pickup_datetime' => $request->pickup_datetime,
                'taxi_type' => $request->taxi_type,
                'status' => $request->status,
                'assigned_driver_id' => $assignedDriverId,
                'assigned_taxi_id' => $assignedTaxiId,
                'estimated_fare' => $request->estimated_fare,
            ]);

            // Notify driver if a new driver was assigned (and not pending)
            if ($originalDriverId != $assignedDriverId && $assignedDriverId !== null) {
                $driver = User::find($assignedDriverId);
                if ($driver) {
                    $driver->notify(new BookingAssignedNotification($booking));
                }
            }

            // Handle status changes
            if ($originalStatus !== $request->status) {
                $this->handleStatusChange($booking, $originalStatus, $request->status);
            }


            // Log the modification
            Log::info('Booking updated by super admin', [
                'booking_id' => $booking->id,
                'admin_id' => auth()->id(),
                'changes' => $request->only([
                    'status',
                    'assigned_driver_id',
                    'estimated_fare',
                ])
            ]);

            DB::commit();

            return redirect()
                ->route('super-admin.bookings.show', $booking)
                ->with('success', 'Réservation mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating booking: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la réservation.');
        }
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
