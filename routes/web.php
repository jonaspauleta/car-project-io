<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::prefix('garage')
        ->group(function () {
            Route::get('/', [GarageController::class, 'index'])->name('index');
            Route::get('/{car}', [GarageController::class, 'show'])->name('show');
            Route::get('/{car}/edit', [GarageController::class, 'edit'])->name('edit');
            Route::post('/', [GarageController::class, 'store'])->name('store');
            Route::put('/{car}', [GarageController::class, 'update'])->name('update');
            Route::delete('/{car}', [GarageController::class, 'destroy'])->name('destroy');

            Route::prefix('{car}/mods')
                ->group(function () {
                    Route::get('/', [ModificationsController::class, 'index'])->name('index');
                    Route::get('/{modification}', [ModificationsController::class, 'show'])->name('show');
                    Route::get('/{modification}/edit', [ModificationsController::class, 'edit'])->name('edit');
                    Route::post('/', [ModificationsController::class, 'store'])->name('store');
                    Route::put('/{modification}', [ModificationsController::class, 'update'])->name('update');
                    Route::delete('/{modification}', [ModificationsController::class, 'destroy'])->name('destroy');
                })->name('mods.');

            /* TODO ideas for later
        Route::prefix('{car}/maintenance')
        ->group(function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('index');
            Route::get('/{maintenance}', [MaintenanceController::class, 'show'])->name('show');
            Route::get('/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('edit');
            Route::post('/', [MaintenanceController::class, 'store'])->name('store');
            Route::put('/{maintenance}', [MaintenanceController::class, 'update'])->name('update');
            Route::delete('/{maintenance}', [MaintenanceController::class, 'destroy'])->name('destroy');
        })->name('maintenance.');

        Route::prefix('{car}/setup')
        ->group(function () {
            Route::get('/', [SetupController::class, 'index'])->name('index');
            Route::get('/{setup}', [SetupController::class, 'show'])->name('show');
            Route::get('/{setup}/edit', [SetupController::class, 'edit'])->name('edit');
            Route::post('/', [SetupController::class, 'store'])->name('store');
            Route::put('/{setup}', [SetupController::class, 'update'])->name('update');
            Route::delete('/{setup}', [SetupController::class, 'destroy'])->name('destroy');
        })->name('setup.');
        */
        })->name('garage.');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
