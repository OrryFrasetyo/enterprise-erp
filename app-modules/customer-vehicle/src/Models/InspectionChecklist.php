<?php

namespace Modules\CustomerVehicle\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\Model;

class InspectionChecklist extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'inspection_checklists';

    protected $fillable = [
        'work_order_id',
        'branch_id',
        'vehicle_id',
        'inspector_id',
        'vehicle_category',
        'fuel_level_percentage',
        'checklist_groups',
        'body_defects',
        'customer_belongings',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'branch_id' => 'integer',
            'vehicle_id' => 'integer',
            'inspector_id' => 'integer',
            'fuel_level_percentage' => 'integer',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');    
    }
}