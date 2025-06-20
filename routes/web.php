<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\TravelPackageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrivateBookingController;
use App\Http\Controllers\Admin\PrivateBookingController as AdminPrivateBookingController;
use App\Http\Controllers\Customer\ReviewController;

// Home Route
Route::get('/', function () {
    return view('customer.home');
})->name('homepage');

// Authentication Routes
Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('post-login', [AuthController::class, 'postLogin'])->name('login.post');
Route::get('registration', [AuthController::class, 'registration'])->name('register');
Route::post('post-registration', [AuthController::class, 'postRegistration'])->name('register.post');
Route::get('dashboard', [AuthController::class, 'dashboard'])->middleware('verified');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Home Route (for authenticated and non-authenticated users)
Route::get('/home', function () {
    if (auth()->check()) {
        return view('customer.home');
    }
    return view('customer.home');
})->name('customer.home');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    \Log::info('Verifying email', [
        'authenticated_user' => auth()->user(),
        'route_id' => $request->route('id'),
        'route_hash' => $request->route('hash'),
        'full_url' => $request->fullUrl(),
        'session' => session()->all(),
    ]);
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
    
    // Corrected Dashboard Route
    Route::get('dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->middleware('auth:admin')->name('dashboard');
    Route::get('dashboard/charts-data', [App\Http\Controllers\AdminController::class, 'getChartsData'])->middleware('auth:admin')->name('dashboard.charts-data');
    Route::get('/admin/dashboard/revenue-data', [AdminController::class, 'getRevenueData'])
        ->name('admin.dashboard.revenue-data')
        ->middleware('auth:admin');
});

// Customer Routes (authenticated and verified)
Route::middleware(['auth', 'verified'])->group(function () {
    // Customer Dashboard
    Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');


    // Booking detail routes
    Route::get('/booking/detail/{id}', [BookingController::class, 'showBookingDetails'])->name('customer.booking.detail');
    Route::get('/booking/private-detail/{id}', [PrivateBookingController::class, 'showBookingDetails'])->name('customer.booking.private-detail');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {
    // Travel Package Management
    Route::resource('travel-package', TravelPackageController::class);
    Route::patch('travel-package/toggle-visibility/{id}', [TravelPackageController::class, 'toggleVisibility'])->name('travel-package.toggle-visibility');
    Route::delete('travel-package/{id}/include/{index}', [TravelPackageController::class, 'deleteIncludeItem'])->name('travel-package.delete-include');
    Route::delete('travel-package/{id}/exclude/{index}', [TravelPackageController::class, 'deleteExcludeItem'])->name('travel-package.delete-exclude');
    Route::delete('travel-package/{id}/date/{index}', [TravelPackageController::class, 'deleteAvailableDate'])->name('travel-package.delete-date');
    
    // Customer Management
    Route::controller(App\Http\Controllers\Customer\CustomerController::class)->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customer.index');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customer.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
    });
    // General Booking Management
    Route::controller(App\Http\Controllers\Admin\BookingController::class)->group(function () {
        Route::get('bookings', 'index')->name('booking.index');
        Route::get('bookings/{booking}', 'show')->name('booking.show');
        Route::patch('bookings/{booking}/status', 'updateStatus')->name('booking.updateStatus');
        Route::delete('bookings/{booking}', 'destroy')->name('booking.destroy');
    });
    
    // Private Booking Management
    Route::controller(AdminPrivateBookingController::class)->prefix('private-booking')->name('private-booking.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{booking}', 'show')->name('show');
        Route::patch('/{booking}/update-status', 'updateStatus')->name('updateStatus');
        Route::delete('/{booking}', 'destroy')->name('destroy');
    });
    
    // FAQ Management
    Route::resource('faqs', App\Http\Controllers\Admin\FaqController::class);


    //Review Management
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{id}', [App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('reviews.show');
    Route::post('/reviews/{id}/reply', [App\Http\Controllers\Admin\ReviewController::class, 'reply'])->name('reviews.reply');
    Route::delete('/reviews/{id}/reply', [App\Http\Controllers\Admin\ReviewController::class, 'deleteReply'])->name('reviews.delete-reply');
    
});

// Route to toggle FAQ visibility
Route::patch('faqs/{faq}/visibility', [App\Http\Controllers\Admin\FaqController::class, 'toggleVisibility'])->name('faqs.visibility');

// Travel Package Routes (Customer)
Route::get('/packages/thailand', [TravelPackageController::class, 'showThailandPackages'])->name('packages.thailand');
Route::get('/packages/vietnam', [TravelPackageController::class, 'showVietnamPackages'])->name('packages.vietnam');
Route::get('/packages/indonesia', [TravelPackageController::class, 'showIndonesiaPackages'])->name('packages.indonesia');
Route::get('/packages/southkorea', [TravelPackageController::class, 'showSouthKoreaPackages'])->name('packages.southkorea');
Route::get('/packages/{country}/{id}', [TravelPackageController::class, 'showPackageDetails'])->name('package.details');

// General Booking Routes (Customer)
Route::middleware(['auth'])->group(function () {
    Route::get('/booking/{packageId}', [BookingController::class, 'showBookingForm'])->name('booking.create');
    Route::post('/booking/store/{packageId}', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('my-booking');
    
    // Payment Routes
    Route::get('/booking/{bookingId}/payment', [PaymentController::class, 'showPaymentPage'])->name('payment.page');
    Route::post('/booking/{bookingId}/process-payment', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::post('/payment/pay-later/{bookingId}', [PaymentController::class, 'payLater'])->name('payment.pay-later');
    Route::get('/booking/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
});

// Private Booking Routes (Customer)
Route::middleware(['auth'])->group(function () {
    Route::get('private-booking/{package}/create', [PrivateBookingController::class, 'create'])->name('private-booking.create');
    Route::post('private-booking/{package}/store', [PrivateBookingController::class, 'store'])->name('private-booking.store');
    
    // Private Booking Payment Routes
    Route::get('/private-booking/{privateBookingId}/payment', [App\Http\Controllers\PrivatePaymentController::class, 'showPaymentPage'])->name('private-booking.payment');
    Route::post('/private-booking/{privateBookingId}/process-payment', [App\Http\Controllers\PrivatePaymentController::class, 'processPayment'])->name('private-booking.process-payment');
    Route::post('/private-booking/{privateBookingId}/pay-later', [App\Http\Controllers\PrivatePaymentController::class, 'payLater'])->name('private-booking.pay-later');
    Route::get('/private-booking/payment/success', [App\Http\Controllers\PrivatePaymentController::class, 'paymentSuccess'])->name('private-payment.success');
});

// General Page Routes
Route::get('/about-us', [PageController::class, 'aboutUs'])->name('about.us');
Route::get('/faqs', [PageController::class, 'faqs'])->name('customer.faqs');

// Customer review routes
// Public review route - accessible to all users
Route::get('/customer/reviews', [ReviewController::class, 'index'])->name('customer.reviews.index');

// Protected review routes - require authentication
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/review/create/{bookingId}', [ReviewController::class, 'create'])->name('review.create');
    Route::get('/review/create/private/{bookingId}', [ReviewController::class, 'create'])->name('review.create.private')->defaults('isPrivate', true);
    Route::post('/review/store/{bookingId}', [ReviewController::class, 'store'])->name('review.store');
    Route::post('/review/store/private/{bookingId}', [ReviewController::class, 'store'])->name('review.store.private')->defaults('isPrivate', true);
    Route::get('/review/edit/{id}', [ReviewController::class, 'edit'])->name('review.edit');
    Route::put('/review/update/{id}', [ReviewController::class, 'update'])->name('review.update');
    Route::delete('/review/destroy/{id}', [ReviewController::class, 'destroy'])->name('review.destroy');
});
