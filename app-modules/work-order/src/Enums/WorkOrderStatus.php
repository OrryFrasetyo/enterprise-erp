<?php

namespace Modules\WorkOrder\Enums;

enum WorkOrderStatus: string
{
    case Queued = 'queued';
    case InProgress = 'in_progress';
    case WaitingParts = 'waiting_parts';
    case QcTesting = 'qc_testing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Queued => 'Antre',
            self::InProgress => 'Sedang Dikerjakan',
            self::WaitingParts => 'Menunggu Suku Cadang',
            self::QcTesting => 'QC / Uji Jalan',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
