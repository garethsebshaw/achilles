<?php

namespace App\Nova\Filters\Logging;

use Carbon\Carbon;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class DateWindowFilter extends Filter
{
    public $component = 'select-filter';

    public function __construct(
        protected string $column = 'created_at',
        protected ?string $label = null,
    ) {
    }

    public function name()
    {
        return $this->label ?? __('Date Window');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return match ($value) {
            '24h' => $query->where($this->column, '>=', Carbon::now()->subDay()),
            '7d' => $query->where($this->column, '>=', Carbon::now()->subDays(7)),
            '30d' => $query->where($this->column, '>=', Carbon::now()->subDays(30)),
            default => $query,
        };
    }

    public function options(NovaRequest $request)
    {
        return [
            __('Last 24 Hours') => '24h',
            __('Last 7 Days') => '7d',
            __('Last 30 Days') => '30d',
        ];
    }
}
