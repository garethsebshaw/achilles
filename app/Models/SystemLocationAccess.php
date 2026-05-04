<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SystemLocationAccess extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'system_location_access';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'location_id',
        'user_id',
        'access_type',
        'access_identifier',
        'access_granted_date',
        'access_expiry_date',
        'granted_by_id',
        'is_active',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'access_granted_date' => 'date',
        'access_expiry_date' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->access_granted_date)) {
                $model->access_granted_date = now();
            }
            if (empty($model->granted_by_id) && auth()->check()) {
                $model->granted_by_id = auth()->id();
            }
        });
    }

    /**
     * Get the location associated with the access record.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(SystemLocation::class, 'location_id');
    }

    /**
     * Get the user associated with the access record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who granted the access.
     */
    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by_id');
    }

    /**
     * Check if the access record has expired.
     */
    public function isExpired(): bool
    {
        if (!$this->access_expiry_date) {
            return false;
        }

        return $this->access_expiry_date->isPast();
    }

    /**
     * Check if the access record is currently valid.
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Get the number of days until the access expires.
     */
    public function getDaysUntilExpiry(): ?int
    {
        if (!$this->access_expiry_date) {
            return null;
        }

        $days = Carbon::now()->diffInDays($this->access_expiry_date, false);
        return $days >= 0 ? $days : null;
    }

    /**
     * Get the duration of access in days.
     */
    public function getAccessDuration(): ?int
    {
        if (!$this->access_expiry_date) {
            return null;
        }

        return $this->access_granted_date->diffInDays($this->access_expiry_date);
    }

    /**
     * Scope a query to only include active access records.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include non-expired access records.
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('access_expiry_date')
                ->orWhere('access_expiry_date', '>=', now());
        });
    }

    /**
     * Scope a query to only include valid access records.
     */
    public function scopeValid($query)
    {
        return $query->active()->notExpired();
    }

    /**
     * Scope a query to only include expired access records.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('access_expiry_date')
            ->where('access_expiry_date', '<', now());
    }

    /**
     * Scope a query to only include access records for a specific access type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('access_type', $type);
    }

    /**
     * Revoke this access record.
     */
    public function revoke(?string $notes = null): bool
    {
        $this->is_active = false;
        if ($notes) {
            $this->notes = $notes;
        }
        return $this->save();
    }

    /**
     * Extend the access expiry date.
     */
    public function extend(Carbon $newExpiryDate, ?string $notes = null): bool
    {
        if ($newExpiryDate->isPast()) {
            return false;
        }

        $this->access_expiry_date = $newExpiryDate;
        if ($notes) {
            $this->notes = $notes;
        }
        return $this->save();
    }

    /**
     * The available access types.
     */
    public static function accessTypes(): array
    {
        return [
            'key' => __('Key'),
            'code' => __('Access Code'),
            'card' => __('Access Card'),
            'fob' => __('Key Fob'),
            'biometric' => __('Biometric'),
            'other' => __('Other')
        ];
    }
}
