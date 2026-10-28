<?php

use App\Http\Controllers\AdminApartmentController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to apartments index
Route::get('/', fn() => redirect()->route('apartments.index'));

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Apartments Public Routes
Route::prefix('apartments')->name('apartments.')->group(function () {
    Route::get('/', [ApartmentController::class, 'index'])->name('index');
    Route::get('/all', [ApartmentController::class, 'all_apartment'])->name('all_apartment');
    Route::get('/{id}', [ApartmentController::class, 'show'])->name('show')->whereNumber('id');
    Route::get('/{apartment}/calendar.ics', [CalendarController::class, 'export'])->name('calendar_ics');
});

// Apartments Authenticated Routes
Route::prefix('apartments')->name('apartments.')->middleware('auth')->group(function () {
    Route::get('/create', [ApartmentController::class, 'create'])->name('create');
    Route::post('/', [ApartmentController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ApartmentController::class, 'edit'])->name('edit')->whereNumber('id');
    Route::put('/{id}', [ApartmentController::class, 'update'])->name('update')->whereNumber('id');
    Route::delete('/{id}', [ApartmentController::class, 'destroy'])->name('destroy')->whereNumber('id');
    Route::delete('/{apartment}/images/{image}', [ApartmentController::class, 'destroyImage'])->name('destroy_image');
});

// Authenticated User Features
Route::middleware('auth')->group(function () {
    // Owner Dashboard & Received bookings
    Route::get('/dashboard', [DashboardController::class, 'owner'])->name('dashboard.owner');
    Route::get('/my-apartments/bookings', [ApartmentController::class, 'myApartmentsWithBookings'])->name('apartments.my_bookings');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_read');

    // Wishlist (Favorites)
    Route::get('/wishlists', [WishlistController::class, 'index'])->name('wishlists.index');
    Route::post('/wishlists/{apartment}/toggle', [WishlistController::class, 'toggle'])->name('wishlists.toggle');

    // Messages / Chat
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/start/{apartment}', [MessageController::class, 'start'])->name('messages.start');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');

    // Invoices
    Route::get('/bookings/{booking}/invoice', [InvoiceController::class, 'show'])->name('bookings.invoice');
    Route::get('/bookings/{booking}/invoice/download', [InvoiceController::class, 'download'])->name('bookings.invoice.download');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// Bookings Routes (User)
Route::prefix('bookings')->name('bookings.')->middleware('auth')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/{apartment_id}/create', [BookingController::class, 'create'])->name('create');
    Route::post('/{apartment_id}', [BookingController::class, 'store'])->name('store');
    Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
    Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
    Route::put('/{booking}', [BookingController::class, 'update'])->name('update');
    Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/confirm', [BookingController::class, 'confirm'])->name('confirm');
    Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->name('cancel');
    Route::post('/{id}/payment-status', [BookingController::class, 'updatePaymentStatus'])->name('update_payment_status');
});

// Admin Routes
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/pending-apartments', [AdminApartmentController::class, 'index'])->name('apartments.pending');
    Route::patch('/apartments/{apartment}/approve', [AdminApartmentController::class, 'approve'])->name('apartments.approve');
    Route::delete('/apartments/{apartment}', [AdminApartmentController::class, 'destroy'])->name('apartments.destroy');
});

// Payments Routes
Route::prefix('payments')->name('payments.')->middleware('auth')->group(function () {
    Route::get('/unpaid', [PaymentsController::class, 'unpaid'])->name('unpaid');
    Route::post('/{booking_id}', [PaymentsController::class, 'pay'])->name('pay');
});
