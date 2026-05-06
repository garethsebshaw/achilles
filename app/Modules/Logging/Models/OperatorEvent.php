<?php

namespace App\Modules\Logging\Models;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorEvent extends Model
{
    use HasFactory;

    protected $table = 'operator_events';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'module_key',
        'event_key',
        'title',
        'description',
        'severity',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
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
