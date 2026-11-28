<?php

namespace Modules\WorkOrder\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\WorkOrder\Enums\WorkOrderStatus;
use Modules\WorkOrder\Events\WorkOrderCompleted;
use Modules\WorkOrder\Events\WorkOrderStatusChanged;
use Modules\WorkOrder\Models\WorkOrder;

class UpdateWorkOrderStatusAction
{
    public function execute(
        User $actor,
        WorkOrder $workOrder,
        WorkOrderStatus $newStatus,
        ?string $mechanicNotes = null
    ): WorkOrder {
        $previousStatus = $workOrder->status;

        // Mencegah pemrosesan ganda jika status tidak berubah (Idempotency)
        if ($previousStatus === $newStatus) {
            return $workOrder;
        }

        $completedEventPayload = null;

        DB::transaction(function () use ($actor, $workOrder, $previousStatus, $newStatus, $mechanicNotes, &$completedEventPayload) {
            $updates = [
                'status' => $newStatus,
                'mechanic_notes' => $mechanicNotes ?? $workOrder->mechanic_notes,
            ];

            if ($newStatus === WorkOrderStatus::InProgress && $workOrder->started_at === null) {
                $updates['started_at'] = now();
            }

            if ($newStatus === WorkOrderStatus::Completed) {
                $updates['completed_at'] = now();

                // Tandai seluruh part yang direservasi menjadi deducted
                $reservedParts = $workOrder->parts()
                    ->where('reservation_status', 'reserved')
                    ->get();

                $workOrder->parts()
                    ->where('reservation_status', 'reserved')
                    ->update(['reservation_status' => 'deducted']);

                $completedEventPayload = new WorkOrderCompleted(
                    workOrderId: $workOrder->id,
                    woNumber: $workOrder->wo_number,
                    branchId: $workOrder->branch_id,
                    customerId: $workOrder->customer_id,
                    actorId: $actor->id,
                    totalLaborCost: (int) $workOrder->services()->sum('labor_cost'),
                    totalPartsCost: (int) $workOrder->parts()->sum('subtotal'),
                    parts: $reservedParts->map(fn ($p) => [
                        'part_id' => $p->part_id,
                        'qty' => $p->qty,
                    ])->all(),
                );
            }

            $workOrder->update($updates);
        });

        // munculkan event
        WorkOrderStatusChanged::dispatch(
            workOrderId: $workOrder->id,
            woNumber: $workOrder->wo_number,
            branchId: $workOrder->branch_id,
            fromStatus: $previousStatus->value,
            toStatus: $newStatus->value,
            actorId: $actor->id,
            actorName: $actor->name,
            notes: $mechanicNotes,
        );

        if ($completedEventPayload !== null) {
            WorkOrderCompleted::dispatch(
                $completedEventPayload->workOrderId,
                $completedEventPayload->woNumber,
                $completedEventPayload->branchId,
                $completedEventPayload->customerId,
                $completedEventPayload->actorId,
                $completedEventPayload->totalLaborCost,
                $completedEventPayload->totalPartsCost,
                $completedEventPayload->parts,
            );
        }

        return $workOrder->fresh();
    }
}