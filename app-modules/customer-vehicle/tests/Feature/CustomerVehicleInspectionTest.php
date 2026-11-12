<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CustomerVehicle\Enums\TransmissionType;
use Modules\CustomerVehicle\Models\Customer;
use Modules\CustomerVehicle\Models\InspectionChecklist;
use Modules\CustomerVehicle\Models\Vehicle;

uses(RefreshDatabase::class);

test('dapat menghubungkan kendaraan di postgresql dengan checklist dinamis di mongodb', function () {
    InspectionChecklist::where('vehicle_category', 'motorcycle_cvt')->delete();

    $customer = Customer::create([
        'phone' => '081234567890',
        'name' => 'Rizky Pratama',
        'address' => 'Padang Utara',
    ]);

    $vehicle = Vehicle::create([
        'customer_id' => $customer->id,
        'license_plate' => '  ba 4567 xy ',
        'vin' => 'MH1JM5110PK123456',
        'engine_number' => 'JM51E-1123456',
        'brand' => 'Honda',
        'model' => 'Vario 160',
        'year' => 2025,
        'transmission_type' => TransmissionType::Cvt,
        'current_odometer' => 12500,
    ]);

    expect($vehicle->license_plate)->toBe('BA 4567 XY')
        ->and($vehicle->transmission_type)->toBe(TransmissionType::Cvt);

    expect(Vehicle::searchByPlate('4567')->count())->toBe(1);

    $vehicle->inspectionChecklists()->create([
        'branch_id' => 1,
        'inspector_id' => 1,
        'vehicle_category' => 'motorcycle_cvt',
        'fuel_level_percentage' => 75,
        'checklist_groups' => [
            'cvt_system' => [
                'v_belt' => 'good',
                'roller_wear' => 'warning',
                'note' => 'Roller mulai peyang, disarankan ganti',
            ],
            'brakes' => [
                'front_pad_mm' => 4.0,
                'rear_pad_mm' => 3.5,
            ],
        ],
        'body_defects' => [
            ['zone' => 'left_cover', 'type' => 'scratch'],
        ],
        'customer_belongings' => ['Helm', 'Sarung tangan'],
    ]);

    $loadedVehicle = Vehicle::with('inspectionChecklists')->find($vehicle->id);

    expect($loadedVehicle->inspectionChecklists)->toHaveCount(1)
        ->and($loadedVehicle->inspectionChecklists->first()->checklist_groups['cvt_system']['roller_wear'])
        ->toBe('warning');
});
