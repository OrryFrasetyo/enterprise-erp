<?php

namespace Modules\WorkOrder\Models;

use App\Models\Branch;
use App\Models\Scopes\BranchScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CustomerVehicle\Models\Customer;
use Modules\CustomerVehicle\Models\InspectionChecklist;
use Modules\CustomerVehicle\Models\Vehicle;
use Modules\WorkOrder\Enums\WorkOrderStatus;
use MongoDB\Laravel\Eloquent\HybridRelations;

#[ScopedBy([BranchScope::class])]
class WorkOrder extends Model
{
    use HybridRelations;

    protected $fillable = [
        'wo_number',
        'branch_id',
        'customer_id',
        'vehicle_id',
        'service_advisor_id',
        'mechanic_id',
        'inspection_mongo_id',
        'queue_number',
        'status',
        'odometer_in',
        'customer_complaint',
        'mechanic_notes',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => WorkOrderStatus::class,
            'queue_number' => 'integer',
            'odometer_in' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(WorkOrderService::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(WorkOrderPart::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function inspectionChecklist()
    {
        return $this->belongsTo(InspectionChecklist::class, 'inspection_mongo_id', '_id');
    }
}
