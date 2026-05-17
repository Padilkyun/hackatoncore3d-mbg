<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RewardManagementController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/bin-monitoring', [DashboardController::class, 'binMonitoring'])->name('bin-monitoring');
Route::get('/route-map', [DashboardController::class, 'routeMap'])->name('route-map');
Route::get('/air-monitoring', [DashboardController::class, 'airMonitoring'])->name('air-monitoring');

Route::get('/reward-management', [RewardManagementController::class, 'index'])->name('reward-management');
Route::get('/reward-management/create', [RewardManagementController::class, 'create'])->name('reward-management.create');
Route::post('/reward-management', [RewardManagementController::class, 'store'])->name('reward-management.store');
Route::get('/reward-management/{id}/edit', [RewardManagementController::class, 'edit'])->name('reward-management.edit');
Route::put('/reward-management/{id}', [RewardManagementController::class, 'update'])->name('reward-management.update');
Route::delete('/reward-management/{id}', [RewardManagementController::class, 'destroy'])->name('reward-management.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
