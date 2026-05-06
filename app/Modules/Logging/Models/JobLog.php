<?php

namespace App\Modules\Logging\Models;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobLog extends Model
{
    use HasFactory;

    protected $table = 'job_logs';

    protected $fillable = [
        'tenant_id',
        'job_uuid',
        'job_class',
        'queue',
        'connection',
        'status',
        'attempts',
        'started_at',
        'finished_at',
        'duration_ms',
        'exception_class',
        'exception_message',
        'trace_excerpt',
        'payload_summary',
        'metadata',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'duration_ms' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'payload_summary' => 'array',
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
