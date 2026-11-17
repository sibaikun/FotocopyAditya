<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::resource('products', ProductController::class)->except('show');

    Route::get('/stock-in', [StockInController::class, 'index'])->name('stock-in.index');
    Route::get('/stock-in/create', [StockInController::class, 'create'])->name('stock-in.create');
    Route::post('/stock-in', [StockInController::class, 'store'])->name('stock-in.store');

    Route::get('/stock-out', [StockOutController::class, 'index'])->name('stock-out.index');
    Route::get('/stock-out/create', [StockOutController::class, 'create'])->name('stock-out.create');
    Route::post('/stock-out', [StockOutController::class, 'store'])->name('stock-out.store');

    Route::resource('services', ServiceController::class)->except('show');
    Route::patch('/services/{service}/toggle', [ServiceController::class, 'toggle'])->name('services.toggle');

    Route::get('/kasir', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/kasir', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/nota', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/nota/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    Route::resource('bahan', SupplyController::class)
    ->parameters(['bahan' => 'supply'])
    ->names('supplies')
    ->except('show');

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/auth.php';
