<?php

use App\Http\Controllers\Agency\BookingController as AgencyBookingController;
use App\Http\Controllers\Agency\DashboardController as AgencyDashboardController;
use App\Http\Controllers\Agency\DriverController as AgencyDriverController;
use App\Http\Controllers\Agency\ReportsController;
use App\Http\Controllers\Agency\TaxiController;
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


// --- Super Admin Routes ---
Route::middleware(['auth', 'role:SUPER_ADMIN'])->prefix('superadmin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/agencies', AgencyController::class);
    Route::patch('/agencies/{agency}/toggle', [AgencyController::class, 'toggleStatus'])->name('agencies.toggle-status');
    Route::patch('/agencies/{agency}/suspend', [AgencyController::class, 'suspend'])->name('agencies.suspend');

    Route::get('/agencies/{agency}/users', [AgencyController::class, 'users'])->name('agencies.users');
    Route::get('/agencies/{agency}/taxis', [AgencyController::class, 'taxis'])->name('agencies.taxis');
    Route::get('/agencies/{agency}/bookings', [AgencyController::class, 'bookings'])->name('agencies.bookings');

    Route::resource('/users', UserController::class);
    Route::patch('/users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
    Route::patch('/users/{user}/unban', [UserController::class, 'unban'])->name('users.unban');
    Route::patch('/users/{user}/toggleStatus', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    Route::resource('/bookings', SuperAdminBookingController::class);
    Route::patch('/bookings/{booking}/assign-driver', [SuperAdminBookingController::class, 'assignDriver'])->name('bookings.assign-driver');
    Route::patch('/bookings/{booking}/change-status', [SuperAdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::get('/bookings/{booking}/applications', [SuperAdminBookingController::class, 'applications'])->name('bookings.applications');
});



// --- Agency Admin Routes ---
Route::middleware(['auth', 'role:AGENCY_ADMIN'])->prefix('agency')->name('agency.')->group(function () {
    // Tableau de bord principal
    Route::get('/dashboard', [AgencyDashboardController::class, 'index'])->name('dashboard');

    // Gestion des chauffeurs (CRUD complet)
    Route::resource('/drivers', AgencyDriverController::class);
    Route::patch('/drivers/{driver}/toggleStatus', [AgencyDriverController::class, 'toggleStatus'])
        ->name('drivers.toggle-status');

    // Gestion des taxis (CRUD complet)
    Route::resource('/taxis', TaxiController::class);
    Route::patch('/taxis/{taxi}/toggleAvailability', [TaxiController::class, 'toggleAvailability'])
        ->name('taxis.toggle-availability');

    // Gestion des bookings (CRUD complet)
    Route::resource('/bookings', AgencyBookingController::class);
    Route::patch('/bookings/{booking}/status', [AgencyBookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::patch('/bookings/{booking}/assign', [AgencyBookingController::class, 'assign'])->name('bookings.assign');
});
