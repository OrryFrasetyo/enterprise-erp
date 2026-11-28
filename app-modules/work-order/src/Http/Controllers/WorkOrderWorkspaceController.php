<?php

namespace Modules\WorkOrder\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Modules\CustomerVehicle\Models\Vehicle;
use Modules\WorkOrder\Actions\CreateWorkOrderAction;
use Modules\WorkOrder\Actions\UpdateWorkOrderStatusAction;
use Modules\WorkOrder\Enums\WorkOrderStatus;
use Modules\WorkOrder\Http\Requests\StoreWorkOrderRequest;
use Modules\WorkOrder\Models\WorkOrder;

class WorkOrderWorkspaceController extends Controller
{
    public function index(Request $request): Response
    {
        $workOrders = WorkOrder::with(['vehicle', 'customer', 'mechanic', 'services', 'branch'])
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $vehicles = Vehicle::with('customer:id,name,phone')
            ->orderBy('license_plate')
            ->get(['id', 'customer_id', 'license_plate', 'brand', 'model', 'current_odometer']);

        $mechanics = User::query()
            ->when($request->user()->branch_id, fn ($q, $branchId) => $q->where('branch_id', $branchId))
            ->where('is_active', true)
            ->get(['id', 'name']);

        return Inertia::render('WorkOrder::Workspace', [
            'workOrders' => $workOrders,
            'vehicles' => $vehicles,
            'mechanics' => $mechanics,
            'currentBranch' => $request->user()->branch?->name ?? 'Semua Cabang (Super Admin)',
        ]);
    }

    public function store(StoreWorkOrderRequest $request, CreateWorkOrderAction $action): RedirectResponse
    {
        $action->execute($request->user(), $request->validated());

        return redirect()
            ->route('work-order.workspace.index')
            ->with('success', 'Work Order baru berhasil dibuat.');
    }

    public function updateStatus(Request $request, WorkOrder $workOrder, UpdateWorkOrderStatusAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(WorkOrderStatus::class)],
            'mechanic_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $action->execute(
            actor: $request->user(),
            workOrder: $workOrder,
            newStatus: WorkOrderStatus::from($validated['status']),
            mechanicNotes: $validated['mechanic_notes'] ?? null,
        );

        return redirect()->route('work-order.workspace.index');
    }
}
