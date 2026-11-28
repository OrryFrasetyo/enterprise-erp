<?php

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditTelemetry\Models\WorkOrderTimeline;
use Modules\CustomerVehicle\Enums\TransmissionType;
use Modules\CustomerVehicle\Models\Customer;
use Modules\CustomerVehicle\Models\Vehicle;
use Modules\Inventory\Models\BranchStock;
use Modules\Inventory\Models\Part;
use Modules\Inventory\Models\StockMovement;
use Modules\WorkOrder\Actions\AddPartToWorkOrderAction;
use Modules\WorkOrder\Actions\CreateWorkOrderAction;
use Modules\WorkOrder\Actions\UpdateWorkOrderStatusAction;
use Modules\WorkOrder\Enums\WorkOrderStatus;

uses(RefreshDatabase::class);

test('orkestrasi reservasi part, pemotongan stok via event, dan audit log mongodb berjalan otomatis', function () {
    $branch = Branch::create([
        'code' => 'PDG-01',
        'name' => 'Cabang Utama Padang',
        'is_active' => true,
    ]);

    $advisor = User::factory()->create([
        'branch_id' => $branch->id,
        'is_active' => true,
    ]);

    $customer = Customer::create([
        'phone' => '081377889900',
        'name' => 'Fajar Nugraha',
    ]);

    $vehicle = Vehicle::create([
        'customer_id' => $customer->id,
        'license_plate' => 'BA 5566 ZZ',
        'vin' => 'MH1JM5566PK123456',
        'engine_number' => 'JM55E-123456',
        'brand' => 'Honda',
        'model' => 'Stylos 160',
        'year' => 2026,
        'transmission_type' => TransmissionType::Cvt,
        'current_odometer' => 3000,
    ]);

    // Siapkan Master Part & Stok Awal Cabang = 10 pcs
    $part = Part::create([
        'sku' => 'OLI-MPX2-08',
        'name' => 'Oli Mesin MPX2 0.8L',
        'category' => 'Lubricant',
        'unit' => 'botol',
        'selling_price' => 65000,
    ]);

    $stock = BranchStock::create([
        'branch_id' => $branch->id,
        'part_id' => $part->id,
        'quantity_on_hand' => 10,
        'quantity_reserved' => 0,
    ]);

    // Buat Work Order
    $workOrder = app(CreateWorkOrderAction::class)->execute($advisor, [
        'vehicle_id' => $vehicle->id,
        'customer_complaint' => 'Ganti oli mesin',
        'services' => [
            ['service_name' => 'Jasa Ganti Oli', 'labor_cost' => 25000],
        ],
    ]);

    // Bersihkan log MongoDB lama jika ada nomor WO yang sama
    WorkOrderTimeline::where('wo_number', $workOrder->wo_number)->delete();

    // Tambahkan 2 botol oli ke Work Order -> stok harus ter-reserve 2
    app(AddPartToWorkOrderAction::class)->execute($advisor, $workOrder, $part->id, 2);

    $stock->refresh();
    expect($stock->quantity_on_hand)->toBe(10)
        ->and($stock->quantity_reserved)->toBe(2)
        ->and($stock->available_quantity)->toBe(8);

    // Pastikan jika meminta melebihi sisa stok tersedia (9 > 8), sistem menolak
    expect(fn () => app(AddPartToWorkOrderAction::class)->execute($advisor, $workOrder, $part->id, 9))
        ->toThrow(RuntimeException::class);

    // Ubah status ke In Progress lalu Completed
    $statusAction = app(UpdateWorkOrderStatusAction::class);
    $statusAction->execute($advisor, $workOrder, WorkOrderStatus::InProgress, 'Mulai bongkar');
    $statusAction->execute($advisor, $workOrder->fresh(), WorkOrderStatus::Completed, 'Oli sudah diganti');

    // Cek hasil akhir di Inventory (PostgreSQL): stok fisik turun jadi 8, reserved kembali ke 0
    $stock->refresh();
    expect($stock->quantity_on_hand)->toBe(8)
        ->and($stock->quantity_reserved)->toBe(0)
        ->and(StockMovement::where('reference_id', $workOrder->id)->count())->toBe(2);

    // Cek hasil akhir di AuditTelemetry (MongoDB): 2 transisi status tercatat otomatis
    $timelines = WorkOrderTimeline::where('wo_number', $workOrder->wo_number)->get();
    expect($timelines)->toHaveCount(2)
        ->and($timelines->last()->transition['to_status'])->toBe('completed');
});