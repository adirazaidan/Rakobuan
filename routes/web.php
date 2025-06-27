<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

// Controller untuk Admin
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OutletController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CallController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Admin\DiningTableController;
use App\Http\Controllers\Admin\OrderItemController;
use App\Http\Controllers\Admin\NotificationController;


// Controller untuk Customer
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CustomerSessionController;
use App\Http\Controllers\Customer\MenuController; // <-- USE INI SEBELUMNYA HILANG

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========================================================================
// RUTE UNTUK PELANGGAN
// ========================================================================
Route::get('/', [CustomerSessionController::class, 'create'])->name('customer.login.form');
Route::post('/login', [CustomerSessionController::class, 'store'])->name('customer.login');
Route::post('/logout', [CustomerSessionController::class, 'destroy'])->name('customer.logout');

// Grup rute yang memerlukan sesi pelanggan
Route::middleware('customer.session')->group(function () {
    Route::get('/menu/{outlet?}', [MenuController::class, 'index'])->name('customer.menu.index');

    // Rute Keranjang
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/cart/update/{productId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');

    // Rute Checkout
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/order/success/{order}', [CheckoutController::class, 'success'])->name('order.success');
    
    // Rute Panggil Pelayan (Dengan path controller yang sudah diperbaiki)
    Route::post('/call-waiter', [\App\Http\Controllers\Customer\CallController::class, 'store'])->name('call.waiter.store');
});

Route::get('/get-available-tables', [CustomerSessionController::class, 'getAvailableTables'])->name('customer.get-tables');


// ========================================================================
// RUTE UNTUK ADMIN
// ========================================================================

Route::prefix('admin')->name('admin.')->group(function () {

    // Rute Login Admin (hanya untuk tamu)
    Route::middleware('guest')->group(function() {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    });

    // Rute Admin yang memerlukan autentikasi
    Route::middleware('auth')->group(function() {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Rute Resource (CRUD)
        Route::resource('outlets', OutletController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('discounts', DiscountController::class);
    
        // Rute Order
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/history', [OrderController::class, 'history'])->name('orders.history');
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
        // Rute Panggilan (Call)
        Route::get('calls', [CallController::class, 'index'])->name('calls.index');
        Route::get('calls/history', [CallController::class, 'history'])->name('calls.history');
        Route::delete('calls/{call}', [CallController::class, 'destroy'])->name('calls.destroy');
        Route::patch('calls/{call}/status', [CallController::class, 'updateStatus'])->name('calls.updateStatus');
    
        // Rute Laporan Penjualan
        Route::get('sales-report', [SalesReportController::class, 'index'])->name('sales.report.index');
   
        Route::resource('dining-tables', DiningTableController::class);
        Route::post('dining-tables/lock-all', [DiningTableController::class, 'lockAll'])->name('dining-tables.lockAll');
        Route::post('dining-tables/unlock-all', [DiningTableController::class, 'unlockAll'])->name('dining-tables.unlockAll');
        Route::post('dining-tables/{diningTable}/clear-session', [DiningTableController::class, 'clearSession'])->name('dining-tables.clearSession');

        Route::get('dining-tables/{diningTable}/render', [DiningTableController::class, 'renderCard'])->name('dining-tables.renderCard');
        Route::post('order-items/{orderItem}/deliver', [OrderItemController::class, 'deliver'])->name('order-items.deliver');
        
        // Route baru untuk mengambil jumlah notifikasi
        Route::get('/notifications/counts', [NotificationController::class, 'getCounts'])->name('notifications.counts');
    });
});

Broadcast::channel('layout-tables', function ($user) {
    return Auth::check();
});
