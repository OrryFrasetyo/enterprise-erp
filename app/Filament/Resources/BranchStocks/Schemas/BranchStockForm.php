<?php

namespace App\Filament\Resources\BranchStocks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BranchStockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->required(),
                Select::make('part_id')
                    ->relationship('part', 'name')
                    ->required(),
                TextInput::make('quantity_on_hand')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('quantity_reserved')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('min_stock_threshold')
                    ->required()
                    ->numeric()
                    ->default(5),
            ]);
    }
}
