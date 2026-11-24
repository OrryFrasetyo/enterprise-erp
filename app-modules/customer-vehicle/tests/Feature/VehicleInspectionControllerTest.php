<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CustomerVehicle\Enums\TransmissionType;
use Modules\CustomerVehicle\Models\Customer;
use Modules\CustomerVehicle\Models\InspectionChecklist;
use Modules\CustomerVehicle\Models\Vehicle;

uses(RefreshDatabase::class);

test('service advisor dapat menyimpan form dynamic checklist ke mongodb dan update odometer', function () {
    InspectionChecklist::where('vehicle_category', 'motorcycle_cvt')->delete();

    $user = User::factory()->create();

    $customer = Customer::create([
        'phone' => '081122334455',
        'name' => 'Budi Santoso',
    ]);

    $vehicle = Vehicle::create([
        'customer_id' => $customer->id,
        'license_plate' => 'BA 1234 QA',
        'vin' => 'MH1JM9999PK000001',
        'engine_number' => 'JM99E-000001',
        'brand' => 'Honda',
        'model' => 'PCX 160',
        'year' => 2025,
        'transmission_type' => TransmissionType::Cvt,
        'current_odometer' => 5000,
    ]);

    $response = $this->actingAs($user)->post(route('customer-vehicle.inspect.store', $vehicle->id), [
        'current_odometer' => 5450,
        'fuel_level_percentage' => 80,
        'checklist_groups' => [
            'cvt_and_engine' => [
                'v_belt_condition' => ['status' => 'good', 'note' => 'Aman'],
            ],
        ],
        'body_defects' => [
            ['zone' => 'Dek Kanan', 'type' => 'scratch', 'note' => 'Baret halus'],
        ],
        'customer_belongings' => ['Helm Cargloss'],
    ]);

    $response->assertRedirect(route('customer-vehicle.inspect.create', $vehicle->id));

    expect($vehicle->fresh()->current_odometer)->toBe(5450);
    expect($vehicle->inspectionChecklists()->count())->toBe(1);
});
