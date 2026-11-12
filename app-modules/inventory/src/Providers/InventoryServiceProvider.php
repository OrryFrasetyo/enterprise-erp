<?php

namespace Modules\Inventory\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Inventory\Models\Part;
use Modules\Inventory\Policies\PartPolicy;

class InventoryServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void {
        Gate::policy(Part::class, PartPolicy::class);
    }
}
