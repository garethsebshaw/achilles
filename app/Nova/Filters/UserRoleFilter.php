<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserRoleFilter extends Filter
{
    public $component = 'select-filter';

    public function apply(Request $request, $query, $value)
    {
        return match ($value) {
            'sys_admin' => $query->where('is_sys_admin', true),
            'admin' => $query->where('is_admin', true),
            'team_lead' => $query->where('is_team_leader', true),
            'guide' => $query->where('is_guide', true),
            'athlete' => $query->where('is_athlete', true),
            'privileged' => $query->where(function ($query) {
                $query->where('is_sys_admin', true)
                    ->orWhere('is_admin', true)
                    ->orWhere('is_team_leader', true);
            }),
            'non_privileged' => $query->where('is_sys_admin', false)
                ->where('is_admin', false)
                ->where('is_team_leader', false),
            default => $query,
        };
    }

    public function options(Request $request): array
    {
        return [
            __('System Admins') => 'sys_admin',
            __('Admins') => 'admin',
            __('Team Leads') => 'team_lead',
            __('Guides') => 'guide',
            __('Athletes') => 'athlete',
            __('Any Privileged User') => 'privileged',
            __('Non-Privileged Users') => 'non_privileged',
        ];
    }

    public function name()
    {
        return __('User Role');
    }
}
