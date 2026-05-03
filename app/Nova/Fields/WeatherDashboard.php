<?php

namespace App\Nova\Fields;

use Laravel\Nova\Fields\Field;

class WeatherDashboard extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'weather-dashboard';

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute ?? 'weather_dashboard', $resolveCallback);

        $this->withMeta([
            'component' => 'weather-dashboard'
        ]);
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, ?string $attribute = null): void
    {
        parent::resolve($resource, $attribute);

        $this->withMeta(['locationId' => $resource->id]);
    }
}
