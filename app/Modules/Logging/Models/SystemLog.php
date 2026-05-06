<?php

namespace App\Modules\Logging\Models;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
    use HasFactory;

    protected $table = 'system_logs';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'module_key',
        'source_type',
        'source_id',
        'level',
        'event_key',
        'message',
        'context',
        'request_id',
        'correlation_id',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
