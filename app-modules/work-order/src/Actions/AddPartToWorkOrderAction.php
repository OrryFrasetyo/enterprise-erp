<?php

namespace Modules\WorkOrder\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Contracts\StockReservationContract;
use Modules\WorkOrder\Models\WorkOrder;
use Modules\WorkOrder\Models\WorkOrderPart;

class AddPartToWorkOrderAction
{
    public function __construct(
        private readonly StockReservationContract $stockReservation
    ){
    }

    public function execute(User $actor, WorkOrder $workOrder, int $partId, int $qty): WorkOrderPart
    {
        return DB::transaction(function () use ($actor, $workOrder, $partId, $qty){
            // modul Inventory mengecek & mengunci stok
            $snapshot = $this->stockReservation->reserveForWorkOrder(
                branchId: $workOrder->branch_id,
                partId: $partId,
                qty: $qty,
                workOrderId: $workOrder->id,
                actorId: $actor->id
            );        
            
            // Simpan snapshot suku cadang ke tabel work_order_parts
            return $workOrder->parts()->create([
                'part_id' => $snapshot->partId,
                'part_sku_snapshot' => $snapshot->sku,
                'part_name_snapshot' => $snapshot->name,
                'qty' => $qty,
                'unit_price_snapshot' => $snapshot->unitPrice,
                'subtotal' => $snapshot->unitPrice * $qty,
                'reservation_status' => 'reserved',
            ]);
        });
    }
}