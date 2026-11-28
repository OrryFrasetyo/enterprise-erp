<?php

namespace Modules\Inventory\Contracts;

readonly class PartSnapshotData
{
    public function __construct(
        public int $partId,
        public string $sku,
        public string $name,
        public int $unitPrice,
    ) {}
}
