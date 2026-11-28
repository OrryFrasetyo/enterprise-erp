<?php

namespace Modules\Inventory\Contracts;

interface StockReservationContract
{
    public function reserveForWorkOrder(
        int $branchId,
        int $partId,
        int $qty,
        int $workOrderId,
        int $actorId
    ): PartSnapshotData;

    public function deductReservedForWorkOrder(
        int $branchId,
        int $partId,
        int $qty,
        int $workOrderId,
        int $actorId
    ): void;
}
