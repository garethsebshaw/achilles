<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemStatus extends Model
{
    protected static array $moduleIdCache = [];

    protected $fillable = [
        'system_module_id',
        'parent_id',
        'name',
        'code',
        'color',
        'sort_order',
        'is_default',
        'is_system',
        'metadata'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_system' => 'boolean',
        'metadata' => 'json',
        'sort_order' => 'integer'
    ];
    public function module(): BelongsTo
    {
        return $this->belongsTo(SystemModule::class, 'system_module_id');
    }

    public function systemModule(): BelongsTo
    {
        return $this->belongsTo(SystemModule::class, 'system_module_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SystemStatus::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SystemStatus::class, 'parent_id');
    }

    public function scopeForModelType(Builder $query, string $modelType): Builder
    {
        $moduleId = static::moduleIdForModel($modelType);

        if ($moduleId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('system_module_id', $moduleId);
    }

    public static function moduleIdForModel(string $modelType): ?int
    {
        if (! array_key_exists($modelType, static::$moduleIdCache)) {
            static::$moduleIdCache[$modelType] = SystemModule::query()
                ->where('model_type', $modelType)
                ->value('id');
        }

        return static::$moduleIdCache[$modelType];
    }

    public static function defaultForModel(string $modelType, array $preferredCodes = []): ?self
    {
        $preferredCodes = array_values(array_unique($preferredCodes));

        $statuses = static::query()
            ->forModelType($modelType)
            ->when($preferredCodes !== [], function (Builder $query) use ($preferredCodes) {
                $query->where(function (Builder $builder) use ($preferredCodes) {
                    $builder->whereIn('code', $preferredCodes)
                        ->orWhere('is_default', true);
                });
            }, function (Builder $query) {
                $query->where('is_default', true);
            })
            ->get();

        if ($statuses->isEmpty()) {
            return static::query()
                ->forModelType($modelType)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->first();
        }

        return $statuses
            ->sortBy(function (self $status) use ($preferredCodes) {
                $preferredIndex = array_search($status->code, $preferredCodes, true);

                if ($preferredIndex !== false) {
                    return $preferredIndex;
                }

                return count($preferredCodes) + ($status->is_default ? 0 : 1) + ($status->sort_order ?? 0);
            })
            ->first();
    }

    public static function findForModel(string $modelType, string $code, array $fallbackCodes = []): ?self
    {
        $candidateCodes = array_values(array_unique(array_filter(array_merge([$code], $fallbackCodes))));

        if ($candidateCodes !== []) {
            $statusesByCode = static::query()
                ->forModelType($modelType)
                ->whereIn('code', $candidateCodes)
                ->get()
                ->keyBy('code');

            foreach ($candidateCodes as $candidateCode) {
                if ($statusesByCode->has($candidateCode)) {
                    return $statusesByCode->get($candidateCode);
                }
            }
        }

        return static::defaultForModel($modelType, $fallbackCodes);
    }

    public static function idForModel(string $modelType, string $code, array $fallbackCodes = []): ?int
    {
        return static::findForModel($modelType, $code, $fallbackCodes)?->getKey();
    }

    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $status = $this;

        while ($status->parent) {
            $category = $status->parent;
            array_unshift($path, $status->name);
        }

        return implode(' > ', $path);
    }

}
