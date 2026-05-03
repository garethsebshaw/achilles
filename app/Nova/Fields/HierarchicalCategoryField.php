<?php

namespace App\Nova\Fields;

use Laravel\Nova\Fields\BelongsTo;
use App\Models\SystemModule;
use App\Models\SystemCategory;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\SystemCategory as SystemCategoryResource;

class HierarchicalCategoryField extends BelongsTo
{
    protected $allowParentSelection = true;

    /**
     * Create a new field.
     *
     * @param string $name
     * @param string|null $attribute
     * @param string|null $resource
     */
    public function __construct($name = 'Category', $attribute = 'category', $resource = null)
    {
        // Important: Use the Nova Resource class here, not the model
        parent::__construct($name, $attribute, \App\Nova\SystemCategory::class);

        $this->resolveUsing(function ($value) {
            return SystemCategory::find($value);
        });

        $this->setRelatableModels();
    }

    /**
     * Allow or disallow parent category selection
     *
     * @param bool $allowed
     * @return $this
     */
    public function allowParentSelection($allowed = true)
    {
        $this->allowParentSelection = $allowed;
        return $this;
    }

    /**
     * Set up the relatable models query
     */
    protected function setRelatableModels()
    {
        $this->withMeta(['relatableOrderDirection' => null]);
        $this->withMeta(['relatableOrder' => null]);

        $this->relatableQueryUsing(function (NovaRequest $request, $query) {
            $modelType = $this->resource ? get_class($this->resource) : null;

            if ($modelType) {
                $module = SystemModule::where('model_type', $modelType)->first();

                if ($module) {
                    // Get all categories and sort them manually
                    $categories = \App\Models\SystemCategory::where('system_module_id', $module->id)
                        ->where('active', true)
                        ->get();

                    // Get parent IDs in the order we want
                    $orderedIds = collect();

                    // Add parents first
                    $parents = $categories->whereNull('parent_id')->sortBy('name');
                    foreach ($parents as $parent) {
                        $orderedIds->push($parent->id);
                        // Add children immediately after their parent
                        $children = $categories->where('parent_id', $parent->id)->sortBy('name');
                        foreach ($children as $child) {
                            $orderedIds->push($child->id);
                        }
                    }
//                    $this->command->info(class_basename(static::class) . ' HierarchicalCategoryField successfully!');

                    // Use whereIn with specific ordering
                    return $query->whereIn('id', $orderedIds)
                        ->orderByRaw("FIELD(id, " . $orderedIds->join(',') . ")");
                }
            }

            return $query->where('active', true);
        });

        $this->displayUsing(function ($category) {
            if (!$category) return '';

            return $category->parent_id
                ? '    └─ ' . $category->name
                : $category->name;
        });
    }

    /**
     * Get the field's component.
     *
     * @return string
     */
    public function component()
    {
        return 'belongs-to-field';
    }
}
