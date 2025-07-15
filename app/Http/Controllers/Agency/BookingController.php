<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\City;
use App\Models\User;
use App\Models\Taxi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Affiche la liste des réservations de l'agence.
     */
    public function index(Request $request)
    {
        $agency = Auth::user()->agency;

        $query = $agency->bookings()->with(['client', 'driver', 'taxi', 'pickupCity', 'destinationCity']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_uuid', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('pickup_location', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('driver_id')) {
            $query->where('assigned_driver_id', $request->driver_id);
        }

        if ($request->filled('taxi_id')) {
            $query->where('assigned_taxi_id', $request->taxi_id);
        }

        if ($request->filled('pickup_city_id')) {
            $query->where('pickup_city_id', $request->pickup_city_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('pickup_datetime', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('pickup_datetime', '<=', $request->date_to);
        }

        $bookings = $query->latest('pickup_datetime')->paginate(15);

        // Données pour les filtres
        $drivers = $agency->drivers()->orderBy('firstname')->get();
        $taxis = $agency->taxis()->orderBy('license_plate')->get();
        $cities = City::orderBy('name')->get();

        return view('agency.bookings.index', compact('bookings', 'drivers', 'taxis', 'cities'));
    }

    /**
     * Affiche les détails d'une réservation.
     */
    public function show(Booking $booking)
    {
        // Vérifier que la réservation appartient à l'agence
        if (!$booking->taxi || $booking->taxi->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation d\'accéder à cette réservation.');
        }

        // Charger les relations
        $booking->load(['client', 'driver', 'taxi', 'pickupCity', 'destinationCity', 'applications.driver']);

        return view('agency.bookings.show', compact('booking'));
    }

    /**
     * Met à jour le statut d'une réservation.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        // Vérifier que la réservation appartient à l'agence
        if (!$booking->taxi || $booking->taxi->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier cette réservation.');
        }

        $request->validate([
            'status' => 'required|in:PENDING,CONFIRMED,IN_PROGRESS,COMPLETED,CANCELLED'
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $booking->status;
            $newStatus = $request->status;

            // Logique métier selon le changement de statut
            if ($newStatus === 'COMPLETED' && $oldStatus !== 'COMPLETED') {
                // Marquer le taxi comme disponible
                if ($booking->taxi) {
                    $booking->taxi->update(['is_available' => true]);
                }
            } elseif ($newStatus === 'IN_PROGRESS' && $oldStatus !== 'IN_PROGRESS') {
                // Marquer le taxi comme occupé
                if ($booking->taxi) {
                    $booking->taxi->update(['is_available' => false]);
                }
            } elseif ($newStatus === 'CANCELLED') {
                // Libérer le taxi
                if ($booking->taxi) {
                    $booking->taxi->update(['is_available' => true]);
                }
            }

            $booking->update(['status' => $newStatus]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Statut de la réservation mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du statut.');
        }
    }

    /**
     * Assigne un chauffeur et/ou un taxi à une réservation.
     */
    public function assign(Request $request, Booking $booking)
    {
        // Vérifier que la réservation appartient à l'agence
        if (!$booking->taxi || $booking->taxi->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de modifier cette réservation.');
        }

        $agency = Auth::user()->agency;

        $request->validate([
            'assigned_driver_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($agency) {
                    if ($value) {
                        $driver = User::find($value);
                        if (!$driver || $driver->agency_id !== $agency->id || !$driver->hasRole('driver')) {
                            $fail('Le chauffeur sélectionné n\'est pas valide.');
                        }
                    }
                }
            ],
            'assigned_taxi_id' => [
                'nullable',
                'exists:taxis,id',
                function ($attribute, $value, $fail) use ($agency) {
                    if ($value) {
                        $taxi = Taxi::find($value);
                        if (!$taxi || $taxi->agency_id !== $agency->id) {
                            $fail('Le taxi sélectionné n\'appartient pas à votre agence.');
                        }
                    }
                }
            ],
        ]);

        try {
            DB::beginTransaction();

            $booking->update([
                'assigned_driver_id' => $request->assigned_driver_id,
                'assigned_taxi_id' => $request->assigned_taxi_id,
            ]);

            // Si on assigne un taxi, le marquer comme occupé si la course est en cours
            if ($request->assigned_taxi_id && in_array($booking->status, ['CONFIRMED', 'IN_PROGRESS'])) {
                $taxi = Taxi::find($request->assigned_taxi_id);
                $taxi->update(['is_available' => false]);
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Attribution mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de l\'attribution.');
        }
    }

    /**
     * Supprime une réservation.
     */
    public function destroy(Booking $booking)
    {
        // Vérifier que la réservation appartient à l'agence
        if (!$booking->taxi || $booking->taxi->agency_id !== Auth::user()->agency_id) {
            abort(403, 'Vous n\'avez pas l\'autorisation de supprimer cette réservation.');
        }

        try {
            DB::beginTransaction();

            // Vérifier que la réservation peut être supprimée
            if (in_array($booking->status, ['IN_PROGRESS'])) {
                return redirect()
                    ->back()
                    ->with('error', 'Impossible de supprimer une réservation en cours.');
            }

            // Libérer le taxi si assigné
            if ($booking->taxi) {
                $booking->taxi->update(['is_available' => true]);
            }

            $bookingUuid = $booking->booking_uuid;
            $booking->delete();

            DB::commit();

            return redirect()
                ->route('agency.bookings.index')
                ->with('success', "Réservation {$bookingUuid} supprimée avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la suppression de la réservation.');
        }
    }
}
