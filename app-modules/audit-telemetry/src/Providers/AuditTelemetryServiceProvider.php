<?php

namespace Modules\AuditTelemetry\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\AuditTelemetry\Listeners\RecordWorkOrderTimeline;
use Modules\WorkOrder\Events\WorkOrderStatusChanged;

class AuditTelemetryServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(WorkOrderStatusChanged::class, RecordWorkOrderTimeline::class);
    }
}
