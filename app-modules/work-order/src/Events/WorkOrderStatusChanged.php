<?php

namespace Modules\WorkOrder\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkOrderStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $workOrderId,
        public readonly string $woNumber,
        public readonly int $branchId,
        public readonly string $fromStatus,
        public readonly string $toStatus,
        public readonly int $actorId,
        public readonly string $actorName,
        public readonly ?string $notes = null,
    ) {}
}
