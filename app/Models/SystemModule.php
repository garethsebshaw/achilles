<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemModule extends Model
{
    use SoftDeletes;

    public const STATE_IMPLEMENTED = 'implemented';
    public const STATE_PARTIAL_SHELL = 'partial_shell';
    public const STATE_MAPPED_ALIAS = 'mapped_alias';
    public const STATE_MISSING_SPEC_ONLY = 'missing_spec_only';

    protected $fillable = [
        'name',
        'model_type',
        'description',
        'active',
        'metadata'
    ];

    protected $casts = [
        'active' => 'boolean',
        'metadata' => 'json'
    ];

    public function getImplementationStateAttribute(): string
    {
        return (string) data_get($this->metadata, 'implementation_state', self::STATE_MISSING_SPEC_ONLY);
    }

    public function getImplementationStatusLabelAttribute(): string
    {
        return match ($this->implementation_state) {
            self::STATE_IMPLEMENTED => __('Implemented'),
            self::STATE_PARTIAL_SHELL => __('Partial Shell'),
            self::STATE_MAPPED_ALIAS => __('Mapped Alias'),
            default => __('Spec Only'),
        };
    }

    public function getImplementationNotesAttribute(): ?string
    {
        $notes = data_get($this->metadata, 'implementation_notes');

        return is_string($notes) && $notes !== '' ? $notes : null;
    }

    public function getCanonicalModelTypeAttribute(): ?string
    {
        $canonical = data_get($this->metadata, 'canonical_model_type');

        return is_string($canonical) && $canonical !== '' ? $canonical : null;
    }

    public function statuses()
    {
        return $this->hasMany(SystemStatus::class, 'system_module_id');
    }

    public function taggables()
    {
        return $this->hasMany(SystemTaggable::class, 'system_module_id');
    }


    public function categories()
    {
        return $this->hasMany(SystemCategory::class, 'system_module_id');
    }

    public function systemCategories()
    {
        return $this->hasMany(SystemCategory::class, 'system_module_id');
    }

    public function systemStatuses()
    {
        return $this->hasMany(SystemStatus::class, 'system_module_id');
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey() :string
    {
        return 'system-modules';
    }
}
