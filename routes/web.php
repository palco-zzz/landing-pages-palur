<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// POS Routes (Protected - All authenticated users)
Route::middleware(['auth', 'verified'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::post('/orders', [PosController::class, 'store'])->name('orders.store');
    Route::post('/sync', [PosController::class, 'sync'])->name('sync');
    Route::get('/unpaid', [PosController::class, 'unpaidOrders'])->name('unpaid');
    Route::post('/orders/{transaction}/add', [PosController::class, 'addItem'])->name('orders.add');
    Route::post('/orders/{transaction}/checkout', [PosController::class, 'checkout'])->name('orders.checkout');
    Route::post('/orders/{transaction}/pay', [PosController::class, 'pay'])->name('orders.pay');
    Route::post('/orders/{transaction}/cancel', [PosController::class, 'cancel'])->name('orders.cancel');
    Route::delete('/item/{item}', [PosController::class, 'voidItem'])->name('void');
    Route::post('/items/batch-void', [PosController::class, 'batchVoid'])->name('items.batch-void');
});

// Transaction History (All authenticated users)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
});

// Admin-Only Routes (Protected by EnsureUserIsAdmin middleware)
Route::middleware(['auth', 'verified', EnsureUserIsAdmin::class])->group(function () {
    // Menu Management
    Route::resource('menus', MenuController::class)->except(['show', 'create', 'edit']);
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);

    // User Management
    Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
});

require __DIR__.'/settings.php';
