<?php

namespace App\Filament\Resources\BranchStocks\Pages;

use App\Filament\Resources\BranchStocks\BranchStockResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBranchStock extends ViewRecord
{
    protected static string $resource = BranchStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
