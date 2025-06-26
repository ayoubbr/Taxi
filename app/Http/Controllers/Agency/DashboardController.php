<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal de l'agence.
     */
    public function index()
    {
        $agency = Auth::user()->agency;

        // Récupérer les statistiques
        $stats = [
            'drivers_count' => $agency->users()
                ->whereHas('role', fn($q) => $q->where('name', 'DRIVER'))->count(),
            'taxis_count' => $agency->taxis()->count(),
            // Vous pouvez ajouter d'autres statistiques ici (réservations, revenus, etc.)
        ];

        return view('agency.dashboard', compact('stats'));
    }
}
