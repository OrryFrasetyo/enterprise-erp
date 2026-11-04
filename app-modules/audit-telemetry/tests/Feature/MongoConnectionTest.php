<?php

use Modules\AuditTelemetry\Models\WorkOrderTimeline;

test('dapat menyimpan data mengambil dokumen log di mongodb', function () {
    WorkOrderTimeline::where('wo_number', 'WO-TEST-001')->delete();

    $log = WorkOrderTimeline::create([
        'event_type' => 'WorkOrderCreated',
        'module_source' => 'WorkOrder',
        'branch_id' => 1,
        'work_order_id' => 999,
        'wo_number' => 'WO-TEST-001',
        'actor' => [
            'user_id' => 1,
            'name' => 'Super Admin',
            'role' => 'super_admin',
        ],
        'occurred_at' => now()->toIso8601String(),
    ]);

    expect($log->id)->not()->toBeNull();

    $fetched = WorkOrderTimeline::where('wo_number', 'WO-TEST-001')->first();
    expect($fetched->actor['name'])->toBe('Super Admin');
});
