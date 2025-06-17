<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingApplicationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

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
Route::middleware('auth', 'client')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('client.bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('client.bookings.create');
    Route::get('/bookings/{uuid}', [BookingController::class, 'show'])->name('client.bookings.show');
    Route::post('/bookings', [BookingController::class, 'store'])->name('client.bookings.store');
    Route::get('/bookings/{uuid}/confirmation', [BookingController::class, 'showConfirmation'])->name('client.bookings.confirmation');
    Route::get('/bookings/{booking}/applications', [BookingController::class, 'showApplications'])->name('client.bookings.applications');
    Route::post('/bookings/{booking}/applications/{application}/accept', [BookingController::class, 'acceptApplication'])->name('client.bookings.accept_application');
    Route::put('/bookings/{uuid}/cancel', [BookingController::class, 'cancel'])->name('client.bookings.cancel');
});


// Driver Specific Routes
Route::middleware(['auth', 'driver'])->group(function () { // Use your custom 'driver' middleware
    Route::get('/driver/dashboard', [DriverController::class, 'assignedBookings'])->name('driver.dashboard');
    // Route::get('/driver/scan-qr', [DriverController::class, 'scanQrCodeForm'])->name('driver.scan.qr');
    // Example in web.php
    Route::get('/driver/scan-qr/{booking_uuid}', [DriverController::class, 'scanQrCodeForm'])->name('driver.scan.qr.form');
    // Route::post('/driver/scan-qr-process', [DriverController::class, 'processQrCodeScan'])->name('driver.scan.qr.process');
    Route::post('/driver/scan-qr', [DriverController::class, 'processQrCodeScan'])->name('driver.scan.qr.process');
    Route::post('/driver/bookings/{booking}/update-status', [DriverController::class, 'updateBookingStatus'])->name('driver.booking.update-status');

    // New routes for available bookings
    Route::get('/driver/bookings/available', [DriverController::class, 'availableBookings'])->name('driver.bookings.available');
    Route::post('/bookings/{booking}/apply', [BookingApplicationController::class, 'store'])->name('driver.bookings.apply');
});
