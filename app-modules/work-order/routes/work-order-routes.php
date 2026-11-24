<?php

use Illuminate\Support\Facades\Route;
use Modules\WorkOrder\Http\Controllers\WorkOrderWorkspaceController;
use Modules\WorkOrder\Http\Middleware\EnsureActiveBranchStaff;

Route::middleware(['web', 'auth', EnsureActiveBranchStaff::class])
    ->prefix('work-orders')
    ->name('work-order.workspace.')
    ->group(function () {
        Route::get('/', [WorkOrderWorkspaceController::class, 'index'])->name('index');
        Route::post('/', [WorkOrderWorkspaceController::class, 'store'])->name('store');
        Route::patch('/{workOrder}/status', [WorkOrderWorkspaceController::class, 'updateStatus'])->name('update-status');
    });
