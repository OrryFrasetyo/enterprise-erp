<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VehicleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer.name')
                    ->label('Customer'),
                TextEntry::make('license_plate'),
                TextEntry::make('vin'),
                TextEntry::make('engine_number'),
                TextEntry::make('brand'),
                TextEntry::make('model'),
                TextEntry::make('year')
                    ->numeric(),
                TextEntry::make('transmission_type')
                    ->badge(),
                TextEntry::make('current_odometer')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
