<?php

namespace Modules\WorkOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderService extends Model
{
    protected $fillable = [
        'work_order_id',
        'service_name',
        'labor_cost',
        'is_completed',
    ];

    protected function casts(): array
    {
        return [
            'labor_cost' => 'integer',
            'is_completed' => 'boolean',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
