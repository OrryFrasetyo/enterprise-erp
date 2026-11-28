<?php

namespace Modules\Inventory\Listeners;

use Modules\Inventory\Contracts\StockReservationContract;
use Modules\WorkOrder\Events\WorkOrderCompleted;

class DeductStockOnWorkOrderCompleted
{
    public function __construct(
        private readonly StockReservationContract $stockReservation
    ) {}

    public function handle(WorkOrderCompleted $event): void
    {
        foreach ($event->parts as $item) {
            $this->stockReservation->deductReservedForWorkOrder(
                branchId: $event->branchId,
                partId: $item['part_id'],
                qty: $item['qty'],
                workOrderId: $event->workOrderId,
                actorId: $event->actorId,
            );
        }
    }
}
