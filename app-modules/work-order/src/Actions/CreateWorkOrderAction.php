<?php

namespace Modules\WorkOrder\Actions;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\CustomerVehicle\Models\Vehicle;
use Modules\WorkOrder\Enums\WorkOrderStatus;
use Modules\WorkOrder\Models\WorkOrder;

class CreateWorkOrderAction
{
    public function execute(User $actor, array $data): WorkOrder
    {
        return DB::transaction(function () use ($actor, $data) {
            $branch = $actor->branch_id
                ? Branch::whereKey($actor->branch_id)->lockForUpdate()->firstOrFail()
                : Branch::query()->orderBy('id')->lockForUpdate()->firstOrFail();

            $vehicle = Vehicle::findOrFail($data['vehicle_id']);

            // Hitung nomor antrean harian per cabang
            $lastQueue = WorkOrder::withoutGlobalScopes()
                ->where('branch_id', $branch->id)
                ->whereDate('created_at', today())
                ->max('queue_number') ?? 0;

            $nextQueue = $lastQueue + 1;
            $woNumber = sprintf(
                'WO-%s-%s-%03d',
                strtoupper($branch->code),
                now()->format('Ymd'),
                $nextQueue
            );

            // Ambil ID dokumen inspeksi terbaru milik kendaraan ini (jika ada)
            $latestInspection = $vehicle->inspectionChecklists()
                ->orderBy('created_at', 'desc')
                ->first();

            $workOrder = WorkOrder::create([
                'wo_number' => $woNumber,
                'branch_id' => $branch->id,
                'customer_id' => $vehicle->customer_id,
                'vehicle_id' => $vehicle->id,
                'service_advisor_id' => $actor->id,
                'mechanic_id' => $data['mechanic_id'] ?? null,
                'inspection_mongo_id' => $latestInspection?->id,
                'queue_number' => $nextQueue,
                'status' => WorkOrderStatus::Queued,
                'odometer_in' => $data['odometer_in'] ?? $vehicle->current_odometer,
                'customer_complaint' => $data['customer_complaint'],
            ]);

            if (! empty($data['services'])) {
                foreach ($data['services'] as $service) {
                    $workOrder->services()->create([
                        'service_name' => $service['service_name'],
                        'labor_cost' => $service['labor_cost'] ?? 0,
                    ]);
                }
            }

            return $workOrder->load(['vehicle', 'customer', 'services']);
        });
    }
}
