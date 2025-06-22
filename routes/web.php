<?php

use Illuminate\Support\Facades\Route;

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


// Controller untuk Customer
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CustomerSessionController;
use App\Http\Controllers\Customer\MenuController; // <-- USE INI SEBELUMNYA HILANG

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// // ========================================================================
// // RUTE UNTUK PELANGGAN (CUSTOMER)
// // ========================================================================

// Route::get('/', [CustomerSessionController::class, 'create'])->name('customer.login.form');
// Route::post('/login', [CustomerSessionController::class, 'store'])->name('customer.login');
// Route::post('/logout', [CustomerSessionController::class, 'destroy'])->name('customer.logout');

// // Grup rute yang memerlukan sesi pelanggan (seperti halaman menu & keranjang)
// Route::middleware('customer.session')->group(function () {
//     Route::get('/menu', [MenuController::class, 'index'])->name('customer.menu.index');
//     Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
//     // RUTE BARU UNTUK HALAMAN KERANJANG
//     Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
//     Route::patch('/cart/update/{productId}', [CartController::class, 'update'])->name('cart.update');
//     Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');

//     // RUTE BARU UNTUK CHECKOUT
//     Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
//     Route::get('/order/success/{order}', [CheckoutController::class, 'success'])->name('order.success');

//     // RUTE BARU UNTUK PANGGIL PELAYAN
//     Route::post('/call-waiter', [\App\Http\Controllers\Customer\CallController::class, 'store'])->name('call.waiter.store');
// });

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [CustomerSessionController::class, 'create'])->name('customer.login.form');
Route::post('/login', [CustomerSessionController::class, 'store'])->name('customer.login');
Route::post('/logout', [CustomerSessionController::class, 'destroy'])->name('customer.logout');

// Grup rute yang memerlukan sesi pelanggan
Route::middleware('customer.session')->group(function () {
    Route::get('/menu', [MenuController::class, 'index'])->name('customer.menu.index');

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
    });
});