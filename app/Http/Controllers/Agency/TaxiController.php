<?php
// app/Http/Controllers/Agency/TaxiController.php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Taxi;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaxiController extends Controller
{
    /**
     * Affiche la liste des taxis de l'agence.
     */
    public function index()
    {
        $agency = Auth::user()->agency;
        $taxis = $agency->taxis()->with(['city', 'driver'])->latest()->paginate(10);
        return view('agency.taxis.index', compact('taxis'));
    }

    /**
     * Affiche le formulaire pour créer un nouveau taxi.
     */
    public function create()
    {
        $agency = Auth::user()->agency;
        // Ne lister que les chauffeurs de l'agence qui n'ont pas encore de taxi
        $drivers = $agency->users()->whereDoesntHave('taxiAssigned')->get();
        $cities = City::orderBy('name')->get();
        return view('agency.taxis.create', compact('drivers', 'cities'));
    }

    /**
     * Enregistre un nouveau taxi.
     */
    public function store(Request $request)
    {
        $agency = Auth::user()->agency;
        $request->validate([
            'license_plate' => 'required|string|max:50|unique:taxis',
            'model' => 'required|string|max:100',
            'type' => 'required|in:standard,van,luxe',
            'city_id' => 'required|exists:cities,id',
            'capacity' => 'required|integer|min:1',
            'driver_id' => 'nullable|exists:users,id',
            'user_id' => 'nullable|exists:users,id | exists:driver, id',
            'agency' => 'required|in:luxe,automo,lovelyauto,inmoto',
        ]);

        $taxi = new Taxi($request->all());
        $taxi->agency_id = $agency->id;
        $taxi->save();

        return redirect()->route('agency.taxis.index')->with('success', 'Taxi créé avec succès.');
    }

    /**
     * Affiche le formulaire pour modifier un taxi.
     */
    public function edit(Taxi $taxi)
    {
        if ($taxi->agency_id !== Auth::user()->agency_id) {
            abort(403);
        }
        $agency = Auth::user()->agency;
        // Chauffeurs de l'agence (y compris celui déjà assigné au taxi)
        $drivers = $agency->users()
            ->where(fn($q) => $q->whereDoesntHave('taxiAssigned')->orWhere('id', $taxi->driver_id))
            ->get();
        $cities = City::orderBy('name')->get();
        return view('agency.taxis.edit', compact('taxi', 'drivers', 'cities'));
    }

    /**
     * Met à jour les informations d'un taxi.
     */
    public function update(Request $request, Taxi $taxi)
    {
        if ($taxi->agency_id !== Auth::user()->agency_id) {
            abort(403);
        }
        $request->validate([
            'license_plate' => ['required', 'string', 'max:50', Rule::unique('taxis')->ignore($taxi->id)],
            'model' => 'required|string|max:100',
            'type' => 'required|in:standard,van,luxe',
            'city_id' => 'required|exists:cities,id',
            'capacity' => 'required|integer|min:1',
            'driver_id' => 'nullable|exists:users,id',
        ]);

        $taxi->update($request->all());

        return redirect()->route('agency.taxis.index')->with('success', 'Taxi mis à jour avec succès.');
    }

    /**
     * Supprime un taxi.
     */
    public function destroy(Taxi $taxi)
    {
        if ($taxi->agency_id !== Auth::user()->agency_id) {
            abort(403);
        }
        $taxi->delete();
        return redirect()->route('agency.taxis.index')->with('success', 'Taxi supprimé avec succès.');
    }
}
