<?php

namespace App\Filament\Resources\BranchStocks;

use App\Filament\Resources\BranchStocks\Pages\CreateBranchStock;
use App\Filament\Resources\BranchStocks\Pages\EditBranchStock;
use App\Filament\Resources\BranchStocks\Pages\ListBranchStocks;
use App\Filament\Resources\BranchStocks\Pages\ViewBranchStock;
use App\Filament\Resources\BranchStocks\Schemas\BranchStockForm;
use App\Filament\Resources\BranchStocks\Schemas\BranchStockInfolist;
use App\Filament\Resources\BranchStocks\Tables\BranchStocksTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Inventory\Models\BranchStock;

class BranchStockResource extends Resource
{
    protected static ?string $model = BranchStock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Branch Stock';

    public static function form(Schema $schema): Schema
    {
        return BranchStockForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BranchStockInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchStocksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBranchStocks::route('/'),
            'create' => CreateBranchStock::route('/create'),
            'view' => ViewBranchStock::route('/{record}'),
            'edit' => EditBranchStock::route('/{record}/edit'),
        ];
    }
}
