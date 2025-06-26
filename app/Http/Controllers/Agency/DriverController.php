<?php
namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class DriverController extends Controller
{
    /**
     * Affiche la liste des chauffeurs de l'agence.
     */
    public function index()
    {
        $agency = Auth::user()->agency;
        $driverRole = Role::where('name', 'DRIVER')->first();

        $drivers = $agency->users()
            ->where('role_id', $driverRole->id)
            ->latest()
            ->paginate(10);

        return view('agency.drivers.index', compact('drivers'));
    }

    /**
     * Affiche le formulaire pour créer un nouveau chauffeur.
     */
    public function create()
    {
        return view('agency.drivers.create');
    }

    /**
     * Enregistre un nouveau chauffeur dans la base de données.
     */
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

        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agency_id' => $agency->id,
            'role_id' => $driverRole->id,
            'status' => 'active', // Ou 'inactive' par défaut si vous voulez une activation manuelle
        ]);

        return redirect()->route('agency.drivers.index')->with('success', 'Chauffeur créé avec succès.');
    }

    /**
     * Affiche le formulaire pour modifier un chauffeur.
     */
    public function edit(User $driver)
    {
        // Sécurité : Vérifier que le chauffeur appartient bien à l'agence de l'admin
        if ($driver->agency_id !== Auth::user()->agency_id) {
            abort(403);
        }
        return view('agency.drivers.edit', compact('driver'));
    }

    /**
     * Met à jour les informations d'un chauffeur.
     */
    public function update(Request $request, User $driver)
    {
        if ($driver->agency_id !== Auth::user()->agency_id) {
            abort(403);
        }

        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore($driver->id)],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users')->ignore($driver->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $driver->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'status' => $request->status,
        ]);

        if ($request->filled('password')) {
            $driver->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('agency.drivers.index')->with('success', 'Chauffeur mis à jour avec succès.');
    }

    /**
     * Supprime un chauffeur.
     */
    public function destroy(User $driver)
    {
        if ($driver->agency_id !== Auth::user()->agency_id) {
            abort(403);
        }
        $driver->delete();
        return redirect()->route('agency.drivers.index')->with('success', 'Chauffeur supprimé avec succès.');
    }
}