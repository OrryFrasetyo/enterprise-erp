<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'category',
        'unit',
        'selling_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function branchStocks(): HasMany
    {
        return $this->hasMany(BranchStock::class);
    }
}
