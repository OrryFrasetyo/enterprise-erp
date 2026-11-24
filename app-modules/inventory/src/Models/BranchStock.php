<?php

namespace Modules\Inventory\Models;

use App\Models\Branch;
use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([BranchScope::class])]
class BranchStock extends Model
{
    protected $fillable = [
        'branch_id',
        'part_id',
        'quantity_on_hand',
        'quantity_reserved',
        'min_stock_threshold',
    ];

    protected function availableQuantity(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity_on_hand - $this->quantity_reserved,
        );
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
