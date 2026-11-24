<?php

namespace Modules\CustomerVehicle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\CustomerVehicle\Http\Requests\StoreInspectionChecklistRequest;
use Modules\CustomerVehicle\Models\Vehicle;

class VehicleInspectionController extends Controller
{
    public function create(Vehicle $vehicle): Response
    {
        $vehicle->load('customer');

        $recentInspection = $vehicle->inspectionChecklists()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('CustomerVehicle::InspectionForm', [
            'vehicle' => $vehicle,
            'recentInspection' => $recentInspection,
        ]);
    }

    public function store(StoreInspectionChecklistRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validated();

        // Update odometer terbaru ke PostgreSQL
        $vehicle->update([
            'current_odometer' => $validated['current_odometer'],
        ]);

        $vehicle->inspectionChecklists()->create([
            'branch_id' => $request->user()->branch_id ?? 1,
            'inspector_id' => $request->user()->id,
            'vehicle_category' => 'motorcycle_'.$vehicle->transmission_type->value,
            'fuel_level_percentage' => $validated['fuel_level_percentage'],
            'checklist_groups' => $validated['checklist_groups'],
            'body_defects' => $validated['body_defects'] ?? [],
            'customer_belongings' => array_values(array_filter($validated['customer_belongings'] ?? [])),
            'created_at' => now()->toIso8601String(),
        ]);

        return redirect()
            ->route('customer-vehicle.inspect.create', $vehicle->id)
            ->with('success', 'Data inspeksi kendaraan berhasil disimpan ke MongoDB.');

    }
}
