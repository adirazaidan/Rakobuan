<?php

use Illuminate\Support\Facades\Route;
// Import semua controller yang digunakan di file ini
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController; // Pastikan ini ada
use App\Http\Controllers\Admin\OutletController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CallController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\Customer\CartController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Grup Rute untuk Admin
Route::prefix('admin')->name('admin.')->group(function () {

    // Rute untuk menampilkan form login (hanya untuk tamu/guest)
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest')
        ->name('login');

    // Rute untuk memproses login
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest');

    // Rute yang memerlukan autentikasi
    Route::middleware('auth')->group(function() {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Logout
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Rute untuk Outlet
        Route::resource('outlets', OutletController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('discounts', DiscountController::class);
    
        // Rute untuk Order
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/history', [OrderController::class, 'history'])->name('orders.history');
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
        // Rute untuk Panggilan (Call)
        Route::get('calls', [CallController::class, 'index'])->name('calls.index');
        Route::get('calls/history', [CallController::class, 'history'])->name('calls.history');
        Route::delete('calls/{call}', [CallController::class, 'destroy'])->name('calls.destroy');
        Route::patch('calls/{call}/status', [CallController::class, 'updateStatus'])->name('calls.updateStatus');
    
        // Rute untuk Laporan Penjualan
        Route::get('sales-report', [SalesReportController::class, 'index'])->name('sales.report.index');
    });

    Route::middleware('customer.session')->group(function () {
    Route::get('/menu', [MenuController::class, 'index'])->name('customer.menu.index');

    // Rute untuk Keranjang
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add'); // <-- TAMBAHKAN INI
});

});