<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agency;
use App\Models\Booking;
use App\Models\Role;
use App\Models\Taxi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:SUPER_ADMIN']);
    }

    public function index()
    {
        // Statistiques générales
        $stats = [
            'total_agencies' => Agency::count(),
            'active_agencies' => Agency::where('status', 'active')->count(),
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'banned_users' => User::where('status', 'banned')->count(),
            'total_drivers' => User::whereHas('role', function ($query) {
                $query->where('name', 'DRIVER');
            })->count(),
            'active_drivers' => User::whereHas('role', function ($query) {
                $query->where('name', 'DRIVER');
            })->where('status', 'active')->count(),
            'total_clients' => User::whereHas('role', function ($query) {
                $query->where('name', 'CLIENT');
            })->count(),
            'active_clients' => User::whereHas('role', function ($query) {
                $query->where('name', 'CLIENT');
            })->where('status', 'active')->count(),
            'total_taxis' => Taxi::count(),
            'revenue_today' => Booking::where('status', 'COMPLETED')
                ->whereDate('created_at', today())->sum('estimated_fare'),
            'available_taxis' => Taxi::where('is_available', true)->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'PENDING')->count(),
            'completed_bookings' => Booking::where('status', 'COMPLETED')->count(),
            'revenue_total' => Booking::where('status', 'COMPLETED')->sum('estimated_fare'),
            'revenue_month' => Booking::where('status', 'COMPLETED')
                ->whereMonth('created_at', now()->month)
                ->sum('estimated_fare'),
        ];

        // Top agences par nombre de réservations
        $topAgencies = Agency::withCount('bookings', 'drivers')
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        // Activités récentes
        $recentBookings = Booking::with(['client', 'driver', 'taxi.agency'])
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::latest()
            ->take(5)
            ->get();

        // Statistiques par mois (6 derniers mois)
        $monthlyStats = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total_bookings'),
            DB::raw('SUM(CASE WHEN status = "COMPLETED" THEN estimated_fare ELSE 0 END) as revenue')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('super-admin.dashboard', compact(
            'stats',
            'topAgencies',
            'recentBookings',
            'monthlyStats',
            'recentUsers'
        ));
    }
}
