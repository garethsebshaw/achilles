<?php

namespace App\Nova\Filters;

use App\Models\SystemChapter;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserChapterAccessFilter extends Filter
{
    public $component = 'select-filter';

    public function apply(Request $request, $query, $value)
    {
        return $query->whereHas('activeLocationAccessRecords.location', function ($query) use ($value) {
            $query->where('chapter_id', $value);
        });
    }

    public function options(Request $request): array
    {
        return SystemChapter::query()
            ->orderBy('name')
            ->pluck('id', 'name')
            ->toArray();
    }

    public function name()
    {
        return __('Accessible Chapter');
    }
}
