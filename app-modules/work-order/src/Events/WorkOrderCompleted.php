<?php

namespace Modules\WorkOrder\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkOrderCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $workOrderId,
        public readonly string $woNumber,
        public readonly int $branchId,
        public readonly int $customerId,
        public readonly int $actorId,
        public readonly int $totalLaborCost,
        public readonly int $totalPartsCost,
        public readonly array $parts,
    )
{
    }
}