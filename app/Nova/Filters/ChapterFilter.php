<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use App\Models\Chapter; // Ensure this model exists and is correct

class ChapterFilter extends Filter
{
    /**
     * The filter's component.
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->whereHas('location', function ($q) use ($value) {
            $q->where('chapter_id', $value);
        });
    }

    /**
     * Get the filter's options.
     */
    public function options(Request $request)
    {
        return \App\Models\SystemChapter::orderBy('name', 'asc')->pluck('id', 'name')->toArray();
    }

    /**
     * Get the name for the filter.
     */
    public function name()
    {
        return __('Chapter');
    }
}
