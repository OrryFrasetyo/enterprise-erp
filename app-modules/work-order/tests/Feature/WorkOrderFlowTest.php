<?php

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CustomerVehicle\Enums\TransmissionType;
use Modules\CustomerVehicle\Models\Customer;
use Modules\CustomerVehicle\Models\Vehicle;
use Modules\WorkOrder\Enums\WorkOrderStatus;
use Modules\WorkOrder\Models\WorkOrder;

uses(RefreshDatabase::class);

test('staf cabang dapat membuat work order dengan nomor antrean berurutan dan memperbarui statusnya', function () {
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
        'phone' => '085211223344',
        'name' => 'Rahmat Hidayat',
    ]);

    $vehicle = Vehicle::create([
        'customer_id' => $customer->id,
        'license_plate' => 'BA 8899 OK',
        'vin' => 'MH1KF8899PK112233',
        'engine_number' => 'KF88E-112233',
        'brand' => 'Honda',
        'model' => 'ADV 160',
        'year' => 2025,
        'transmission_type' => TransmissionType::Cvt,
        'current_odometer' => 8000,
    ]);

    // Create first WO
    $response = $this->actingAs($advisor)->post('/work-orders', [
        'vehicle_id' => $vehicle->id,
        'customer_complaint' => 'Ganti oli dan cek CVT',
        'services' => [
            ['service_name' => 'Servis CVT', 'labor_cost' => 85000],
        ],
    ]);

    $response->assertRedirect(route('work-order.workspace.index'));

    $workOrder = WorkOrder::first();
    expect($workOrder)->not->toBeNull()
        ->and($workOrder->queue_number)->toBe(1)
        ->and($workOrder->wo_number)->toContain('WO-PDG-01-')
        ->and($workOrder->status)->toBe(WorkOrderStatus::Queued)
        ->and($workOrder->services)->toHaveCount(1);

    // Pindahkan status ke In Progress lalu Completed
    $this->actingAs($advisor)
        ->patch("/work-orders/{$workOrder->id}/status", ['status' => 'in_progress'])
        ->assertRedirect();

    expect($workOrder->fresh()->started_at)->not->toBeNull();

    $this->actingAs($advisor)
        ->patch("/work-orders/{$workOrder->id}/status", ['status' => 'completed'])
        ->assertRedirect();

    expect($workOrder->fresh()->status)->toBe(WorkOrderStatus::Completed)
        ->and($workOrder->fresh()->completed_at)->not->toBeNull();
});
