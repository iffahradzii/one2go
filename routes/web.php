<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\TravelPackageController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrivateBookingController; // Add this line



// Home Route
Route::get('/', function () {
    return view('customer.home');
})->name('homepage');;

// Authentication Routes
Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('post-login', [AuthController::class, 'postLogin'])->name('login.post');
Route::get('registration', [AuthController::class, 'registration'])->name('register');
Route::post('post-registration', [AuthController::class, 'postRegistration'])->name('register.post');
Route::get('dashboard', [AuthController::class, 'dashboard'])->middleware('verified'); // Add 'verified' middleware to restrict access until email is verified
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Home Route (for authenticated and non-authenticated users)
Route::get('/home', function () {
    if (auth()->check()) {
        return view('customer.home'); // Redirect to the dashboard for authenticated users
    }
    return view('customer.home');  // Show the home page for non-authenticated users
})->name('customer.home');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    
    // Explicitly update the email_verified_at timestamp
    $user = $request->user();
    $user->email_verified_at = now();
    $user->save();
    
    return redirect('/dashboard')->with('success', 'Email verified successfully!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Forgot Password Routes
Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');

// Reset Password Routes
Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [AdminAuthController::class, 'dashboard'])->middleware('auth:admin')->name('dashboard');
   
});

// Customer Routes (authenticated and verified)
Route::middleware(['auth', 'verified'])->group(function () {
    // Customer Dashboard
    Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
});

// admin travel package
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin'])
    ->group(function () {
        Route::resource('travel-package', TravelPackageController::class);
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        
        // Consolidated booking routes
        Route::controller(App\Http\Controllers\Admin\BookingController::class)->group(function () {
            Route::get('bookings', 'index')->name('booking.index');
            Route::get('bookings/{booking}', 'show')->name('booking.show');
            Route::patch('bookings/{booking}/status', 'updateStatus')->name('booking.updateStatus');
            Route::delete('bookings/{booking}', 'destroy')->name('booking.destroy');
        });
    });

    // Add this route
    Route::patch('/admin/travel-package/toggle-visibility/{id}', [App\Http\Controllers\Admin\TravelPackageController::class, 'toggleVisibility'])
        ->name('admin.travel-package.toggle-visibility');
    Route::delete('/admin/travel-package/{travel_package}', [TravelPackageController::class, 'destroy'])->name('admin.travel-package.destroy');

// Route for editing customer
Route::get('/admin/customer/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customer.edit');

// Route for updating customer
Route::put('/admin/customer/{customer}', [CustomerController::class, 'update'])->name('admin.customer.update');

// Route for the customer index page
Route::get('/admin/customer', [CustomerController::class, 'index'])->name('admin.customer.index');


// Route to display the list of Thailand travel packages
Route::get('/packages/thailand', [TravelPackageController::class, 'showThailandPackages'])->name('packages.thailand');

// Route to display the list of Vietnam travel packages
Route::get('/packages/vietnam', [TravelPackageController::class, 'showVietnamPackages'])->name('packages.vietnam');

// Route to display the list of Indonesia travel packages
Route::get('/packages/indonesia', [TravelPackageController::class, 'showIndonesiaPackages'])->name('packages.indonesia');

// Route to display the list of South Korea travel packages
Route::get('/packages/southkorea', [TravelPackageController::class, 'showSouthKoreaPackages'])->name('packages.southkorea');


Route::get('/packages/{country}/{id}', [TravelPackageController::class, 'showPackageDetails'])->name('package.details');


// Booking Routes
Route::get('/booking/{packageId}', [BookingController::class, 'showBookingForm'])
    ->middleware('auth')
    ->name('booking.form');


//General Page Route
Route::get('/about-us', [PageController::class, 'aboutUs'])->name('about.us');


// Booking routes
//Route::get('/booking/form/{packageId}', [BookingController::class, 'showBookingForm'])->name('booking.form');
Route::post('/booking/store/{packageId}', [BookingController::class, 'storeBooking'])->name('booking.store');
Route::get('/my-bookings', [BookingController::class, 'showMyBookings'])->name('my-booking');

// Payment routes
Route::get('/payment/{bookingId}', [PaymentController::class, 'showPaymentPage'])->name('payment.page');
Route::post('/payment/process/{bookingId}', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::post('/payment/pay-later/{bookingId}', [PaymentController::class, 'payLater'])->name('payment.pay-later');

// Existing routes
Route::get('booking/{package}', [BookingController::class, 'create'])->name('booking.create');
Route::post('booking/{package}', [BookingController::class, 'store'])->name('booking.store');

// New private booking routes
Route::get('private-booking/{package}', [PrivateBookingController::class, 'create'])->name('private-booking.create');
Route::post('private-booking/{package}', [PrivateBookingController::class, 'store'])->name('private-booking.store');

// Delete specific items from travel packages
Route::delete('admin/travel-package/{id}/include/{index}', [TravelPackageController::class, 'deleteIncludeItem'])->name('admin.travel-package.delete-include');
Route::delete('admin/travel-package/{id}/exclude/{index}', [TravelPackageController::class, 'deleteExcludeItem'])->name('admin.travel-package.delete-exclude');
Route::delete('admin/travel-package/{id}/date/{index}', [TravelPackageController::class, 'deleteAvailableDate'])->name('admin.travel-package.delete-date');
