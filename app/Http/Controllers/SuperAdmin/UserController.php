<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $query = User::with('agency');

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
                //   ->orWhere('phone', 'like', "%{$search}%");
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

        // Filtre par agence
        if ($request->filled('agency')) {
            $query->where('agency_id', $request->agency);
        }

        $users = $query->latest()->paginate(10);

        $agencies = Agency::orderBy('name')->get();

        $stats = [
            'total_users' => User::count(),
            'drivers' => User::whereHas('role', function ($q) {
                $q->where('name', 'DRIVER');
            })->count(),
            'clients' => User::whereHas('role', function ($q) {
                $q->where('name', 'CLIENT');
            })->count(),
            'banned_users' => User::where('status', 'banned')->count(),
        ];

        return view('super-admin.users.index', compact('users', 'agencies', 'stats'));
    }

    public function show(User $user)
    {
        $user->load(['agency' => function ($query) {
            $query->latest()->take(10);
        }]);

        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'completed_bookings' => $user->bookings()->where('status', 'COMPLETED')->count(),
            'cancelled_bookings' => $user->bookings()->where('status', 'CANCELLED')->count(),
            'total_spent' => $user->role === 'client'
                ? $user->bookings()->where('status', 'COMPLETED')->sum('estimated_fare')
                : 0,
            'total_earned' => $user->role === 'driver'
                ? $user->driverBookings()->where('status', 'COMPLETED')->sum('estimated_fare')
                : 0,
        ];

        return view('super-admin.users.show', compact('user', 'stats'));
    }

    public function create()
    {
        $agencies = Agency::where('status', 'active')->orderBy('name')->get();
        return view('super-admin.users.create', compact('agencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:client,driver,admin',
            'agency_id' => 'nullable|exists:agencies,id',
            'status' => 'required|in:active,inactive,banned',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Utilisateur créé avec succès!');
    }

    public function edit(User $user)
    {
        $agencies = Agency::where('status', 'active')->orderBy('name')->get();
        return view('super-admin.users.edit', compact('user', 'agencies'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:client,driver,admin',
            'agency_id' => 'nullable|exists:agencies,id',
            'status' => 'required|in:active,inactive,banned',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('super-admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès!');
    }

    public function destroy(User $user)
    {
        // Empêcher la suppression du super admin connecté
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Vérifier s'il y a des réservations actives
        if ($user->bookings()->whereIn('status', ['PENDING', 'ASSIGNED', 'IN_PROGRESS'])->count() > 0) {
            return back()->with('error', 'Impossible de supprimer un utilisateur avec des réservations actives.');
        }

        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès!');
    }

    public function ban(User $user)
    {
        // dd('Ban method triggered');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas bannir votre propre compte.');
        }

        $user->update(['status' => 'suspended']);
        // dd($user);

        return back()->with('success', 'Utilisateur suspendu avec succès!');
    }

    public function unban(User $user)
    {
        $user->update(['status' => 'active']);

        return back()->with('success', 'Utilisateur débanni avec succès!');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier le statut de votre propre compte.');
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Utilisateur activé' : 'Utilisateur désactivé';

        return back()->with('success', $message . ' avec succès!');
    }
}
