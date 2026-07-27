<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| 1. RUTE PUBLIK & TAMU (Bisa diakses siapa saja)
|--------------------------------------------------------------------------
*/
Route::get('/', [CustomerController::class, 'index'])->name('home');

// Rute Otentikasi (Hanya untuk tamu yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 2. RUTE PELANGGAN (Wajib Login sebagai Pelanggan)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/checkout/preview', [CustomerController::class, 'checkoutPreview'])->name('checkout.preview');
    Route::post('/checkout/store', [CustomerController::class, 'storeCheckout'])->name('checkout.store');
    
    Route::get('/status', [CustomerController::class, 'status'])->name('customer.status');
    Route::get('/pesanan-saya', [CustomerController::class, 'history'])->name('customer.history');
});

/*
|--------------------------------------------------------------------------
| 3. RUTE ADMIN / PENJUAL (Wajib Login & Ber-role 'Penjual')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Kelola Pesanan & KDS Dapur
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::post('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    
    // Rute Ubah Pembayaran Jadi Lunas
    Route::patch('/orders/{id}/pay', [AdminController::class, 'updatePaymentStatus'])->name('orders.pay'); // <-- PINDAHKAN KESINI
    
    // Cetak Struk Thermal
    Route::get('/orders/{id}/print', [AdminController::class, 'printReceipt'])->name('orders.print');
    
    // Toggle Status Warung Buka / Tutup
    Route::post('/toggle-warung-status', [AdminController::class, 'toggleWarungStatus'])->name('toggle.warung');
    
    // Ekspor Excel Laporan Pesanan
    Route::get('/reports/export-excel', [AdminController::class, 'exportExcel'])->name('reports.excel');
    
    // Kelola Menu (CRUD)
    Route::get('/menus', [AdminController::class, 'menus'])->name('menus');
    Route::post('/menus', [AdminController::class, 'storeMenu'])->name('menus.store');
    Route::put('/menus/{id}', [AdminController::class, 'updateMenu'])->name('menus.update');
    Route::delete('/menus/{id}', [AdminController::class, 'deleteMenu'])->name('menus.destroy');
    
    // Kelola Pelanggan
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
    Route::delete('/customers/{id}', [AdminController::class, 'deleteCustomer'])->name('customers.destroy');
});