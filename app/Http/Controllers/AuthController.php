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
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


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

            $user = Auth::user();

            // Check if user is active
            if ($user->status != 'active') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Your account has been deactivated. Please contact support.'],
                ]);
            }

            // Redirect based on user role
            $redirectUrl = $this->getRedirectUrlByRole($user);

            return redirect()->intended($redirectUrl)->with('success', 'Welcome back, ' . $user->firstname . '!');
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }


    protected function getRedirectUrlByRole($user)
    {
        // Load the role relationship if not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        $roleName = $user->role->name ?? null;

        switch ($roleName) {
            case 'SUPER_ADMIN':
                return route('super-admin.dashboard');

            case 'AGENCY_ADMIN':
                // Check if user has an agency
                if (!$user->agency_id) {
                    Auth::logout();
                    throw ValidationException::withMessages([
                        'email' => ['Your account is not associated with any agency. Please contact support.'],
                    ]);
                }
                return route('agency.dashboard');

            case 'DRIVER':
                // Check if driver has an agency
                if (!$user->agency_id) {
                    Auth::logout();
                    throw ValidationException::withMessages([
                        'email' => ['Your driver account is not associated with any agency. Please contact support.'],
                    ]);
                }
                return route('driver.dashboard');

            case 'CLIENT':
                return route('home');

            default:
                // Fallback for users without proper roles
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Your account does not have a valid role assigned. Please contact support.'],
                ]);
        }
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
            'status' => 'active',
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
