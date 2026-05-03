<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\CheckInUser;
use App\Events\CheckOutUser;
use App\Listeners\HandleCheckIn;
use App\Listeners\HandleCheckOut;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CheckInUser::class => [
            HandleCheckIn::class,
        ],
        CheckOutUser::class => [
            HandleCheckOut::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
