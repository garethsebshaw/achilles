<?php

namespace App\Nova\Fields;

use Laravel\Nova\Fields\BelongsTo;
use App\Models\SystemModule;
use App\Models\SystemCategory;
use Laravel\Nova\Http\Requests\NovaRequest;

class SystemCategoryField extends BelongsTo
{
    public function __construct($name = 'Category', $attribute = 'category', $resource = null)
    {
        parent::__construct($name, $attribute, SystemCategory::class);

        $this->resolveUsing(function ($value) {
            return SystemCategory::find($value);
        });

        $this->setRelatableModels();
    }

    protected function setRelatableModels()
    {
        $this->relatableQueryUsing(function (NovaRequest $request, $query) {
            $modelType = $this->resource ? get_class($this->resource) : null;

            if ($modelType) {
                $module = SystemModule::where('model_type', $modelType)->first();
                if ($module) {
                    return $query->where('system_module_id', $module->id)
                        ->where('active', true);
                }
            }

            return $query->where('active', true);
        });
    }
}
