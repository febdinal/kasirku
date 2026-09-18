<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // POS Kasir
    Route::get('/kasir', [PosController::class, 'index'])->name('pos.index');
    Route::get('/kasir/products', [PosController::class, 'getProducts'])->name('pos.products');
    Route::post('/kasir/store', [PosController::class, 'store'])->name('pos.store');

    // Transaksi
    Route::get('/transaksi/{transaction}/struk', [TransactionController::class, 'printReceipt'])->name('transactions.receipt');
    Route::patch('/transaksi/{transaction}/void', [TransactionController::class, 'void'])->name('transactions.void');
    Route::resource('transaksi', TransactionController::class)
        ->only(['index', 'show'])
        ->names('transactions')
        ->parameters(['transaksi' => 'transaction']);

    // Produk
    Route::resource('produk', ProductController::class)
        ->except(['show'])
        ->names('products')
        ->parameters(['produk' => 'product']);

    // Kategori
    Route::resource('kategori', CategoryController::class)
        ->except(['create', 'edit', 'show'])
        ->names('categories')
        ->parameters(['kategori' => 'category']);

    // Pelanggan
    Route::resource('pelanggan', CustomerController::class)
        ->except(['create', 'edit', 'show'])
        ->names('customers')
        ->parameters(['pelanggan' => 'customer']);

    // Laporan
    Route::get('/laporan/harian', [ReportController::class, 'daily'])->name('reports.daily');
    Route::get('/laporan/bulanan', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/laporan/laba-rugi', [ReportController::class, 'profit'])->name('reports.profit');
    Route::get('/laporan/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');

    // Pengaturan
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/pengaturan/metode-pembayaran', [SettingController::class, 'storePaymentMethod'])->name('settings.payment-methods.store');
    Route::patch('/pengaturan/metode-pembayaran/{paymentMethod}/toggle', [SettingController::class, 'togglePaymentMethod'])->name('settings.payment-methods.toggle');
    Route::delete('/pengaturan/metode-pembayaran/{paymentMethod}', [SettingController::class, 'destroyPaymentMethod'])->name('settings.payment-methods.destroy');

    // Profil User
    Route::get('/profil', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/profil', [UserController::class, 'update'])->name('user.update');
    Route::put('/profil/password', [UserController::class, 'updatePassword'])->name('user.update-password');
});
