<?php

declare(strict_types=1);

use App\Http\Controllers\Frontend\CarController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\ModificationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('cars')
        ->name('cars.')
        ->group(function () {
            Route::get('/', [CarController::class, 'index'])->name('index');
            Route::get('/create', [CarController::class, 'create'])->name('create');
            Route::get('/{car}', [CarController::class, 'show'])->name('show');
            Route::get('/{car}/edit', [CarController::class, 'edit'])->name('edit');
            Route::post('/', [CarController::class, 'store'])->name('store');
            Route::put('/{car}', [CarController::class, 'update'])->name('update');
            Route::delete('/{car}', [CarController::class, 'destroy'])->name('destroy');
            Route::get('/{car}/image', [CarController::class, 'image'])->name('image');

            Route::prefix('{car}/modifications')
                ->name('modifications.')
                ->group(function () {
                    Route::get('/', [ModificationController::class, 'index'])->name('index');
                    Route::get('/create', [ModificationController::class, 'create'])->name('create');
                    Route::get('/{modification}', [ModificationController::class, 'show'])->name('show');
                    Route::get('/{modification}/edit', [ModificationController::class, 'edit'])->name('edit');
                    Route::post('/', [ModificationController::class, 'store'])->name('store');
                    Route::put('/{modification}', [ModificationController::class, 'update'])->name('update');
                    Route::delete('/{modification}', [ModificationController::class, 'destroy'])->name('destroy');
                });

            /* TODO ideas for later
            Route::prefix('{car}/maintenance')
            ->group(function () {
                Route::get('/', [MaintenanceController::class, 'index'])->name('index');
                Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
                Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
                Route::get('/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('edit');
                Route::post('/', [MaintenanceController::class, 'store'])->name('store');
                Route::put('/{maintenance}', [MaintenanceController::class, 'update'])->name('update');
                Route::delete('/{maintenance}', [MaintenanceController::class, 'destroy'])->name('destroy');
            })->name('maintenance.');

            Route::prefix('{car}/setup')
            ->group(function () {
                Route::get('/', [SetupController::class, 'index'])->name('index');
                Route::get('/create', [SetupController::class, 'create'])->name('create');
                Route::get('/{setup}', [SetupController::class, 'show'])->name('show');
                Route::get('/{setup}/edit', [SetupController::class, 'edit'])->name('edit');
                Route::post('/', [SetupController::class, 'store'])->name('store');
                Route::put('/{setup}', [SetupController::class, 'update'])->name('update');
                Route::delete('/{setup}', [SetupController::class, 'destroy'])->name('destroy');
            })->name('setup.');
            */
        });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
