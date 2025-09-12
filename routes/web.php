<?php

declare(strict_types=1);

use App\Http\Controllers\CarController;
use App\Http\Controllers\ModificationController;
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
            Route::get('/', [CarController::class, 'index'])->name('cars.index');
            Route::get('/{car}', [CarController::class, 'show'])->name('cars.show');
            Route::get('/{car}/edit', [CarController::class, 'edit'])->name('cars.edit');
            Route::post('/', [CarController::class, 'store'])->name('cars.store');
            Route::put('/{car}', [CarController::class, 'update'])->name('cars.update');
            Route::delete('/{car}', [CarController::class, 'destroy'])->name('cars.destroy');

            Route::prefix('{car}/mods')
                ->group(function () {
                    Route::get('/', [ModificationController::class, 'index'])->name('modifications.index');
                    Route::get('/{modification}', [ModificationController::class, 'show'])->name('modifications.show');
                    Route::get('/{modification}/edit', [ModificationController::class, 'edit'])->name('modifications.edit');
                    Route::post('/', [ModificationController::class, 'store'])->name('modifications.store');
                    Route::put('/{modification}', [ModificationController::class, 'update'])->name('modifications.update');
                    Route::delete('/{modification}', [ModificationController::class, 'destroy'])->name('modifications.destroy');
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
