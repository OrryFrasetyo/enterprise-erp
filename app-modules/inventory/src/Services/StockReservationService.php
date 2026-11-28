<?php

namespace Modules\Inventory\Services;

use Illuminate\Support\Facades\DB;
use Modules\Inventory\Contracts\PartSnapshotData;
use Modules\Inventory\Contracts\StockReservationContract;
use Modules\Inventory\Models\BranchStock;
use Modules\Inventory\Models\StockMovement;
use RuntimeException;

class StockReservationService implements StockReservationContract
{
    public function reserveForWorkOrder(
        int $branchId,
        int $partId,
        int $qty,
        int $workOrderId,
        int $actorId
    ): PartSnapshotData {
        return DB::transaction(function () use ($branchId, $partId, $qty, $workOrderId, $actorId) {
            $stock = BranchStock::withoutGlobalScopes()
                ->with('part')
                ->where('branch_id', $branchId)
                ->where('part_id', $partId)
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                throw new RuntimeException('Suku cadang tidak terdaftar pada stok cabang ini.');
            }

            if ($stock->available_quantity < $qty) {
                throw new RuntimeException(
                    "Stok {$stock->part->name} tidak mencukupi. Tersedia: {$stock->available_quantity}, diminta: {$qty}."
                );
            }

            $stock->increment('quantity_reserved', $qty);

            StockMovement::create([
                'branch_stock_id' => $stock->id,
                'reference_type' => 'work_order',
                'reference_id' => $workOrderId,
                'movement_type' => 'reserve',
                'quantity' => $qty,
                'actor_id' => $actorId,
                'notes' => "Reservasi stok untuk Work Order #{$workOrderId}",
            ]);

            return new PartSnapshotData(
                partId: $stock->part->id,
                sku: $stock->part->sku,
                name: $stock->part->name,
                unitPrice: $stock->part->selling_price,
            );
        });
    }

    public function deductReservedForWorkOrder(
        int $branchId,
        int $partId,
        int $qty,
        int $workOrderId,
        int $actorId
    ): void {
        DB::transaction(function () use ($branchId, $partId, $qty, $workOrderId, $actorId) {
            $stock = BranchStock::withoutGlobalScopes()
                ->where('branch_id', $branchId)
                ->where('part_id', $partId)
                ->lockForUpdate()
                ->firstOrFail();

            $stock->decrement('quantity_on_hand', $qty);
            $stock->decrement('quantity_reserved', min($qty, $stock->quantity_reserved));

            StockMovement::create([
                'branch_stock_id' => $stock->id,
                'reference_type' => 'work_order',
                'reference_id' => $workOrderId,
                'movement_type' => 'deduct',
                'quantity' => $qty,
                'actor_id' => $actorId,
                'notes' => "Pemotongan stok final dari penyelesaian Work Order #{$workOrderId}",
            ]);
        });
    }
}
