<?php

namespace Modules\AuditTelemetry\Listeners;

use Modules\AuditTelemetry\Models\WorkOrderTimeline;
use Modules\WorkOrder\Events\WorkOrderStatusChanged;

class RecordWorkOrderTimeline
{
    public function handle(WorkOrderStatusChanged $event): void
    {
        WorkOrderTimeline::create([
            'event_type' => 'WorkOrderStatusChanged',
            'module_source' => 'WorkOrder',
            'branch_id' => $event->branchId,
            'work_order_id' => $event->workOrderId,
            'wo_number' => $event->woNumber,
            'actor' => [
                'user_id' => $event->actorId,
                'name' => $event->actorName,
            ],
            'transition' => [
                'from_status' => $event->fromStatus,
                'to_status' => $event->toStatus,
            ],
            'payload_snapshot' => [
                'notes' => $event->notes,
            ],
            'occurred_at' => now()->toIso8601String(),
        ]);
    }
}