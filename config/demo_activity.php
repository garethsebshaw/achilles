<?php

return [
    'enabled' => env('DEMO_ACTIVITY_ENABLED', false),

    'history_days' => (int) env('DEMO_ACTIVITY_HISTORY_DAYS', 365),
    'future_days' => (int) env('DEMO_ACTIVITY_FUTURE_DAYS', 28),

    'daily_new_users_min' => (int) env('DEMO_ACTIVITY_DAILY_NEW_USERS_MIN', 1),
    'daily_new_users_max' => (int) env('DEMO_ACTIVITY_DAILY_NEW_USERS_MAX', 10),

    'athletes' => [
        'minimum' => (int) env('DEMO_ACTIVITY_MIN_ATHLETES', 10),
        'maximum' => (int) env('DEMO_ACTIVITY_MAX_ATHLETES', 110),
        'heavy_session_chance' => (float) env('DEMO_ACTIVITY_HEAVY_SESSION_CHANCE', 0.08),
    ],

    'guides' => [
        'minimum_multiplier' => (float) env('DEMO_ACTIVITY_GUIDE_MIN_MULTIPLIER', 0.65),
        'maximum_multiplier' => (float) env('DEMO_ACTIVITY_GUIDE_MAX_MULTIPLIER', 1.25),
        'maximum_per_athlete' => (int) env('DEMO_ACTIVITY_MAX_GUIDES_PER_ATHLETE', 3),
    ],
];
