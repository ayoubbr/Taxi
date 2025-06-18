<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            // Redirect based on user type (you'll expand this later)
            // For now, simple redirect to home or a dashboard
            return redirect()->intended(route('home'))->with('success', 'You are logged in!');
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }


    public function showRegistrationForm()
    {
        return view('auth.register');
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:client,driver,agency',
            'terms' => 'accepted',
        ]);

        // Determine role_id based on user_type
        $role = Role::where('name', strtoupper($request->user_type))->first();
        if (!$role) {
            return back()->withInput()->withErrors(['user_type' => 'Invalid user type selected.']);
        }

        $user = User::create([
            'username' => strtolower(str_replace(' ', '', $request->name)) . rand(100, 999), // Simple username generation
            'firstname' => explode(' ', $request->name, 2)[0],
            'lastname' => isset(explode(' ', $request->name, 2)[1]) ? explode(' ', $request->name, 2)[1] : null,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect(route('home'))->with('success', 'Registration successful! Welcome.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('home'))->with('success', 'You have been logged out.');
    }
}
