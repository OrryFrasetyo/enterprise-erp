<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomerVehicle\Http\Controllers\VehicleInspectionController;

Route::middleware(['web', 'auth'])
    ->prefix('vehicles/{vehicle}/inspect')
    ->name('customer-vehicle.inspect.')
    ->group(function () {
        Route::get('/', [VehicleInspectionController::class, 'create'])->name('create');
        Route::post('/', [VehicleInspectionController::class, 'store'])->name('store');
    });
