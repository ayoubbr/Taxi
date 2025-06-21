<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingApplicationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\AgencyController;
use App\Http\Controllers\SuperAdmin\BookingController as SuperAdminBookingController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




// Example protected route (for logged-in users)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return "Welcome to your dashboard!";
    })->name('dashboard');
});

// Client-side booking application management
// Route::middleware('auth', 'client')->group(function () {
//     Route::get('/bookings', [BookingController::class, 'index'])->name('client.bookings.index');
//     Route::get('/bookings/create', [BookingController::class, 'create'])->name('client.bookings.create');
//     Route::get('/bookings/{uuid}', [BookingController::class, 'show'])->name('client.bookings.show');
//     Route::post('/bookings', [BookingController::class, 'store'])->name('client.bookings.store');
//     Route::get('/bookings/{uuid}/confirmation', [BookingController::class, 'showConfirmation'])->name('client.bookings.confirmation');
//     Route::get('/bookings/{booking}/applications', [BookingController::class, 'showApplications'])->name('client.bookings.applications');
//     Route::post('/bookings/{booking}/applications/{application}/accept', [BookingController::class, 'acceptApplication'])->name('client.bookings.accept_application');
//     Route::put('/bookings/{uuid}/cancel', [BookingController::class, 'cancel'])->name('client.bookings.cancel');

//     Route::get('/client/profile', [ProfileController::class, 'clientProfile'])->name('client.profile');
//     Route::put('/client/profile', [ProfileController::class, 'updateClientProfile'])->name('client.profile.update');
// });


// --- Client Routes ---
Route::middleware(['auth', 'role:CLIENT'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('client.bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('client.bookings.create');
    Route::get('/bookings/{uuid}', [BookingController::class, 'show'])->name('client.bookings.show');
    Route::post('/bookings', [BookingController::class, 'store'])->name('client.bookings.store');
    Route::get('/bookings/{uuid}/confirmation', [BookingController::class, 'showConfirmation'])->name('client.bookings.confirmation');
    Route::get('/bookings/{booking}/applications', [BookingController::class, 'showApplications'])->name('client.bookings.applications');
    Route::post('/bookings/{booking}/applications/{application}/accept', [BookingController::class, 'acceptApplication'])->name('client.bookings.accept_application');
    Route::put('/bookings/{uuid}/cancel', [BookingController::class, 'cancel'])->name('client.bookings.cancel');

    Route::get('/client/profile', [ProfileController::class, 'clientProfile'])->name('client.profile');
    Route::put('/client/profile', [ProfileController::class, 'updateClientProfile'])->name('client.profile.update');
});



// Driver Specific Routes
// Route::middleware(['auth', 'driver'])->group(function () { // Use your custom 'driver' middleware
//     Route::get('/driver/dashboard', [DriverController::class, 'assignedBookings'])->name('driver.dashboard');
//     Route::get('/driver/scan-qr/{booking_uuid}', [DriverController::class, 'scanQrCodeForm'])->name('driver.scan.qr.form');
//     Route::post('/driver/scan-qr', [DriverController::class, 'processQrCodeScan'])->name('driver.scan.qr.process');
//     Route::post('/driver/bookings/{booking}/update-status', [DriverController::class, 'updateBookingStatus'])->name('driver.booking.update-status');

//     // New routes for available bookings
//     Route::get('/driver/bookings/available', [DriverController::class, 'availableBookings'])->name('driver.bookings.available');
//     Route::post('/bookings/{booking}/apply', [BookingApplicationController::class, 'store'])->name('driver.bookings.apply');

//     // Driver Profile Routes
//     Route::get('/driver/profile', [ProfileController::class, 'driverPorfile'])->name('driver.profile');
//     Route::put('/driver/profile', [ProfileController::class, 'updateDriverProfile'])->name('driver.profile.update');
// });

// --- Driver Routes ---
Route::middleware(['auth', 'role:DRIVER'])->group(function () {
    Route::get('/driver/dashboard', [DriverController::class, 'assignedBookings'])->name('driver.dashboard');
    Route::get('/driver/scan-qr/{booking_uuid}', [DriverController::class, 'scanQrCodeForm'])->name('driver.scan.qr.form');
    Route::post('/driver/scan-qr', [DriverController::class, 'processQrCodeScan'])->name('driver.scan.qr.process');
    Route::post('/driver/bookings/{booking}/update-status', [DriverController::class, 'updateBookingStatus'])->name('driver.booking.update-status');

    // New routes for available bookings
    Route::get('/driver/bookings/available', [DriverController::class, 'availableBookings'])->name('driver.bookings.available');
    Route::post('/bookings/{booking}/apply', [BookingApplicationController::class, 'store'])->name('driver.bookings.apply');

    // Driver Profile Routes
    Route::get('/driver/profile', [ProfileController::class, 'driverPorfile'])->name('driver.profile');
    Route::put('/driver/profile', [ProfileController::class, 'updateDriverProfile'])->name('driver.profile.update');
});

// --- Agency Admin Routes ---
Route::middleware(['auth', 'role:AGENCY_ADMIN'])->prefix('agency')->name('agency.')->group(function () {
    // Exemple : Route::get('/dashboard', [AgencyDashboardController::class, 'index'])->name('dashboard');
    // Exemple : Route::resource('/taxis', AgencyTaxiController::class);
});

// SUPER ADMIN ROUTES
// Route::get('/superadmin/dashboard', [DashboardController::class, 'index'])->name('super-admin.dashboard');
// Route::get('/superadmin/agencies', [AgencyController::class, 'index'])->name('super-admin.agencies.index');
// Route::get('/superadmin/agencies/create', [AgencyController::class, 'create'])->name('super-admin.agencies.create');
// Route::get('/superadmin/users', [UserController::class, 'index'])->name('super-admin.users.index');
// Route::get('/superadmin/users/create', [UserController::class, 'create'])->name('super-admin.users.create');
// Route::get('/superadmin/dashboard/bookings', [DashboardController::class, 'bookings'])->name('super-admin.bookings.index');

// --- Super Admin Routes ---
Route::middleware(['auth', 'role:SUPER_ADMIN'])->prefix('superadmin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('super-admin.dashboard');
    // Route::get('/superadmin/agencies', [AgencyController::class, 'index'])->name('super-admin.agencies.index');
    // Route::get('/superadmin/agencies/create', [AgencyController::class, 'create'])->name('super-admin.agencies.create');
    // Route::get('/superadmin/users', [UserController::class, 'index'])->name('super-admin.users.index');
    // Route::get('/superadmin/users/create', [UserController::class, 'create'])->name('super-admin.users.create');
    // Route::get('/superadmin/dashboard/bookings', [DashboardController::class, 'bookings'])->name('super-admin.bookings.index');

    Route::resource('/agencies', AgencyController::class);
    Route::patch('/agencies/{agency}/toggle', [AgencyController::class, 'toggleStatus'])->name('agencies.toggle-status');
    Route::patch('/agencies/{agency}/suspend', [AgencyController::class, 'suspend'])->name('agencies.suspend');

    Route::get('/agencies/{agency}/users', [AgencyController::class, 'users'])->name('agencies.users');
    Route::get('/agencies/{agency}/taxis', [AgencyController::class, 'taxis'])->name('agencies.taxis');
    Route::get('/agencies/{agency}/bookings', [AgencyController::class, 'bookings'])->name('agencies.bookings');

    Route::resource('/users', UserController::class);
    Route::resource('/bookings', SuperAdminBookingController::class);
    Route::patch('/bookings/{booking}/assign-driver', [SuperAdminBookingController::class, 'assignDriver'])->name('bookings.assign-driver');
    Route::patch('/bookings/{booking}/change-status', [SuperAdminBookingController::class, 'changeStatus'])->name('bookings.change-status');
    Route::get('/bookings/{booking}/applications', [SuperAdminBookingController::class, 'applications'])->name('bookings.applications');
});
