<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the driver's profile.
     */
    public function driverPorfile()
    {
        $driver = Auth::user();
        $taxi = $driver->taxi; // Assumes a 'taxi' relationship exists on the User model
        $cities = array_keys(config('cities.proximity_map'));
        sort($cities);

        return view('driver.profile', compact('driver', 'taxi', 'cities'));
    }

    /**
     * Update the driver's profile information.
     */
    public function updateDriverProfile(Request $request)
    {
        $driver = Auth::user();
        $taxi = $driver->taxi;
        // dd($request->all());
        // // dd($driver);
        // dd($taxi);
        $rules = [
            // User fields validation
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore($driver->id)],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users')->ignore($driver->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];

        if ($driver->taxi) {
            $rules = array_merge($rules, [
                // Taxi fields validation
                'license_plate' => ['required', 'string', 'max:50', $taxi ? Rule::unique('taxis')->ignore($taxi->id) : 'unique:taxis,license_plate'],
                'model' => 'required|string|max:100',
                'capacity' => 'required|integer|min:1',
                'type' => 'required|in:standard,van,luxe',
                'city' => 'required|string|max:100',
            ]);
        }
        $data = $request->validate($rules);

        // Update User Information
        $driver->firstname = $request->firstname;
        $driver->lastname = $request->lastname;
        $driver->username = $request->username;
        $driver->email = $request->email;

        if ($request->filled('password')) {
            $driver->password = Hash::make($request->password);
        }

        $driver->save();

        // Update Taxi Information
        if ($driver->taxi) {
            $driver->taxi()->update(
                ['driver_id' => $driver->id], // Find taxi by driver_id
                [ // Data to update or create with
                    'license_plate' => $request->license_plate,
                    'model' => $request->model,
                    'capacity' => $request->capacity,
                    'type' => $request->type,
                    'city_id' => City::where('name', $request->city)->first()->id,
                ]
            );
        }
        return redirect()->route('driver.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the form for editing the client's profile.
     */
    public function clientProfile()
    {
        $client = Auth::user();
        return view('client.profile', compact('client'));
    }

    /**
     * Update the client's profile information.
     */
    public function updateClientProfile(Request $request)
    {
        $client = Auth::user();

        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'username' => ['required', 'string', 'max:100', Rule::unique('users')->ignore($client->id)],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users')->ignore($client->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        // Update User Information
        $client->firstname = $request->firstname;
        $client->lastname = $request->lastname;
        $client->username = $request->username;
        $client->email = $request->email;

        if ($request->filled('password')) {
            $client->password = Hash::make($request->password);
        }

        $client->save();

        return redirect()->route('client.profile')->with('success', 'Profile updated successfully!');
    }
}
