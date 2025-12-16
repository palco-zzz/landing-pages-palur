<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// POS Routes (Protected)
Route::middleware(['auth', 'verified'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::post('/orders', [PosController::class, 'store'])->name('orders.store');
    Route::get('/unpaid', [PosController::class, 'unpaidOrders'])->name('unpaid');
    Route::post('/orders/{transaction}/add', [PosController::class, 'addItem'])->name('orders.add');
    Route::post('/orders/{transaction}/checkout', [PosController::class, 'checkout'])->name('orders.checkout');
    Route::post('/orders/{transaction}/pay', [PosController::class, 'pay'])->name('orders.pay');
    Route::post('/orders/{transaction}/cancel', [PosController::class, 'cancel'])->name('orders.cancel');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('menus', MenuController::class)->except(['show', 'create', 'edit']);
    Route::patch('/menus/{menu}/toggle', [MenuController::class, 'toggle'])->name('menus.toggle');
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);
});

require __DIR__.'/settings.php';

