<?php

namespace App\Modules\Logging\Models;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleHealthCheck extends Model
{
    use HasFactory;

    protected $table = 'module_health_checks';

    protected $fillable = [
        'tenant_id',
        'module_key',
        'status',
        'severity',
        'summary',
        'details',
        'last_checked_at',
        'resolved_at',
    ];

    protected $casts = [
        'details' => 'array',
        'last_checked_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
