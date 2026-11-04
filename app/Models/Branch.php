<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'is-active',
    ];

    protected function casts(): array
    {
        return [
            'is-active' => 'boolean',
        ];
    }

    protected function users()
    {
        return $this->hasMany(User::class);
    }
}
