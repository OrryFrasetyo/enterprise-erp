<?php

namespace Modules\CustomerVehicle\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CustomerVehicle\Enums\TransmissionType;
use MongoDB\Laravel\Eloquent\HybridRelations;

class Vehicle extends Model
{
    use HybridRelations;

    protected $fillable = [
        'customer_id',
        'license_plate',
        'vin',
        'engine_number',
        'brand',
        'model',
        'year',
        'transmission_type',
        'current_odometer',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'current_odometer' => 'integer',
            'transmission_type' => TransmissionType::class,
        ];
    }

    protected function licensePlate(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtoupper($value),
            set: fn (string $value) => strtoupper(preg_replace('/\s+/', ' ', trim($value))),
        );
    }

    public function scopeSearchByPlate(Builder $query, string $keyword): Builder
    {
        return $query->where('license_plate', 'ILIKE', '%' . trim($keyword) . '%');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function inspectionChecklists()
    {
        return $this->hasMany(InspectionChecklist::class, 'vehicle_id', 'id');
    }
}