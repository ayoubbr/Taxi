<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal de l'agence.
     */
    // public function index()
    // {
    //     $agency = Auth::user()->agency;

    //     // Récupérer les statistiques
    //     $stats = $agency->getStats();
    //     $recentActivities = [];

    //     return view('agency.dashboard', compact('stats', 'recentActivities'));
    // }

    public function index()
    {
        $agency = Auth::user()->agency;

        if (!$agency) {
            // Gérer le cas où, pour une raison quelconque, l'admin n'a pas d'agence
            abort(404, 'Agence non trouvée.');
        }

        // --- Récupérer les statistiques ---
        $driverRoleId = Role::where('name', 'DRIVER')->value('id');
        $stats = $agency->getStats();


        // --- Récupérer les activités récentes ---

        // 1. Nouveaux chauffeurs ajoutés
        $recentDrivers = $agency->drivers()
            // ->where('role_id', $driverRoleId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($user) {
                return (object)[
                    'type' => 'Nouveau Chauffeur',
                    'description' => "Le chauffeur {$user->firstname} {$user->lastname} a été ajouté.",
                    'timestamp' => $user->created_at,
                    'icon' => 'fa-user-plus',
                    'color' => 'blue',
                    'link' => route('agency.drivers.edit', $user->id)
                ];
            });

        // 2. Nouveaux taxis ajoutés
        $recentTaxis = $agency->taxis()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($taxi) {
                return (object)[
                    'type' => 'Nouveau Taxi',
                    'description' => "Le taxi avec la plaque {$taxi->license_plate} a été ajouté.",
                    'timestamp' => $taxi->created_at,
                    'icon' => 'fa-taxi',
                    'color' => 'yellow',
                    'link' => route('agency.taxis.edit', $taxi->id)
                ];
            });

        // 3. Dernières réservations (complétées ou annulées)
        $recentBookings = Booking::whereIn('status', ['COMPLETED', 'CANCELLED'])
            ->whereHas('driver.agency', fn($q) => $q->where('id', $agency->id))
            ->with(['driver', 'client']) // Charger les relations pour éviter N+1 requêtes
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->map(function ($booking) {
                $statusText = $booking->status === 'COMPLETED' ? 'complétée' : 'annulée';
                $driverName = $booking->driver ? $booking->driver->firstname : 'un chauffeur';
                $clientName = $booking->client ? $booking->client->firstname : 'un client';
                return (object)[
                    'type' => "Course " . ucfirst(strtolower($statusText)),
                    'description' => "La course pour {$clientName} par {$driverName} a été {$statusText}.",
                    'timestamp' => $booking->updated_at,
                    'icon' => $booking->status === 'COMPLETED' ? 'fa-check-circle' : 'fa-times-circle',
                    'color' => $booking->status === 'COMPLETED' ? 'green' : 'red',
                    'link' => '#' // Mettre un lien vers la page de détails de la réservation si elle existe
                ];
            });

        // 4. Fusionner, trier et prendre les 10 plus récentes activités
        $allActivities = $recentDrivers->merge($recentTaxis)->merge($recentBookings);
        $recentActivities = $allActivities->sortByDesc('timestamp')->take(10);

        return view('agency.dashboard', compact('stats', 'recentActivities'));
    }
}
