<?php

namespace Modules\WorkOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderPart extends Model
{
    protected $fillable = [
        'work_order_id',
        'part_id',
        'part_sku_snapshot',
        'part_name_snapshot',
        'qty',
        'unit_price_snapshot',
        'subtotal',
        'reservation_status',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_price_snapshot' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
