<?php

namespace App\Nova\Actions;

use App\Support\DemoData\DemoSessionSignupGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Collection;

class GenerateDemoSessionSignups extends Action
{
    use InteractsWithQueue, Queueable;

    public function __construct(private readonly ?DemoSessionSignupGenerator $generator = null)
    {
    }

    public function name()
    {
        return __('Generate Demo Signups');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $result = ($this->generator ?? app(DemoSessionSignupGenerator::class))->generateForSessions($models, [
            'minimum_athletes' => (int) $fields->minimum_athletes,
            'maximum_athletes' => (int) $fields->maximum_athletes,
            'maximum_guides_per_athlete' => (int) $fields->maximum_guides_per_athlete,
            'heavy_session_mode' => (bool) $fields->heavy_session_mode,
        ]);

        return Action::message(__(
            'Generated :signups signups across :sessions sessions (:athletes athletes, :guides guides).',
            [
                'signups' => $result['signups'],
                'sessions' => $result['sessions'],
                'athletes' => $result['athletes'],
                'guides' => $result['guides'],
            ]
        ));
    }

    public function fields(NovaRequest $request)
    {
        return [
            Number::make(__('Minimum Athletes'), 'minimum_athletes')
                ->min(1)
                ->step(1)
                ->default(8)
                ->rules('required', 'integer', 'min:1'),

            Number::make(__('Maximum Athletes'), 'maximum_athletes')
                ->min(1)
                ->step(1)
                ->default(24)
                ->rules('required', 'integer', 'min:1'),

            Number::make(__('Maximum Guides Per Athlete'), 'maximum_guides_per_athlete')
                ->min(1)
                ->step(1)
                ->default(3)
                ->rules('required', 'integer', 'min:1'),

            Boolean::make(__('Heavy Session Mode'), 'heavy_session_mode')
                ->help(__('Generate larger attendance blocks so some sessions have very large signup volumes.'))
                ->default(false),
        ];
    }
}
