<?php

namespace Modules\Inventory\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Inventory\Contracts\StockReservationContract;
use Modules\Inventory\Models\Part;
use Modules\Inventory\Policies\PartPolicy;
use Modules\Inventory\Services\StockReservationService;

class InventoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StockReservationContract::class, StockReservationService::class);
    }

    public function boot(): void
    {
        Gate::policy(Part::class, PartPolicy::class);
    }
}
