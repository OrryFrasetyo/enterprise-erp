<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\CustomerVehicle\Enums\TransmissionType;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                TextInput::make('license_plate')
                    ->required(),
                TextInput::make('vin')
                    ->required(),
                TextInput::make('engine_number')
                    ->required(),
                TextInput::make('brand')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                Select::make('transmission_type')
                    ->options(TransmissionType::class)
                    ->required(),
                TextInput::make('current_odometer')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
