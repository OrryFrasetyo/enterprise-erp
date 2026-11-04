<?php

namespace Modules\AuditTelemetry\Models;

use MongoDB\Laravel\Eloquent\Model;

class WorkOrderTimeline extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'work_order_timelines';

    protected $fillable = [
        'event_type',
        'module_source',
        'branch_id',
        'work_order_id',
        'wo_number',
        'actor',
        'transition',
        'payload_snapshot',
        'occurred_at',
    ];
}
