<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PayHereController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminBookingController;

// Booking UI
Route::get('/', [BookingController::class, 'form'])->name('booking.form');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/success', [BookingController::class, 'success'])->name('booking.success');
Route::get('/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

Route::get('/get-month-price', [BookingController::class, 'getMonthPrice'])
    ->name('booking.month.price');

Route::get('/admin/reports', [\App\Http\Controllers\AdminBookingController::class, 'reports'])
    ->name('admin.reports');


// PayHere notify (server-to-server POST)
Route::post('/payhere/notify', [PayHereController::class, 'notify'])->name('payhere.notify');
Route::post('/payhere/ipn', [PayHereController::class, 'notify']);

// Admin auth
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin panel (protected)
Route::middleware('adminauth')->group(function () {
    Route::get('/admin', fn() => redirect()->route('admin.bookings'))->name('admin.home');

    Route::get('/admin/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings');
    Route::get('/admin/bookings/{id}', [AdminBookingController::class, 'show'])->name('admin.booking.show');
    Route::post('/admin/bookings/{id}/resend', [AdminBookingController::class, 'resendTicket'])->name('admin.booking.resend');

    // Pricing page (optional UI)
    Route::get('/admin/prices', [AdminBookingController::class, 'prices'])->name('admin.prices');
    Route::post('/admin/prices/save', [AdminBookingController::class, 'savePrice'])->name('admin.prices.save');

    Route::get('/admin/disabled-dates', [AdminBookingController::class, 'disabledDates'])->name('admin.disabled_dates');
    Route::post('/admin/disabled-dates/add', [AdminBookingController::class, 'addDisabledDate'])->name('admin.disabled_dates.add');
    Route::post('/admin/disabled-dates/delete/{id}', [AdminBookingController::class, 'deleteDisabledDate'])->name('admin.disabled_dates.delete');
});
