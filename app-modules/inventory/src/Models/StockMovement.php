<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'branch_stock_id',
        'reference_type',
        'reference_id',
        'movement_type',
        'quantity',
        'actor_id',
        'notes',

    ];

    public function branchStock(): BelongsTo
    {
        return $this->belongsTo(BranchStock::class);
    }
}
