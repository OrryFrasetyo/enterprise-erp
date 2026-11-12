<?php

namespace App\Filament\Resources\BranchStocks\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BranchStockInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('branch.name')
                    ->label('Branch'),
                TextEntry::make('part.name')
                    ->label('Part'),
                TextEntry::make('quantity_on_hand')
                    ->numeric(),
                TextEntry::make('quantity_reserved')
                    ->numeric(),
                TextEntry::make('min_stock_threshold')
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
