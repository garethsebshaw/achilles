<?php

return [
    'enabled' => true,
    'database_logging' => true,
    'default_tenant_key' => env('RSC_DEFAULT_TENANT_KEY', 'achilles'),
    'redact_context_keys' => [
        'password',
        'secret',
        'token',
        'api_key',
        'apikey',
        'authorization',
        'refresh_token',
        'access_token',
        'private_key',
        'client_secret',
    ],
    'operator_events' => [
        'auto_create_for_critical_logs' => true,
        'auto_create_for_failed_jobs' => true,
        'auto_create_for_unhealthy_modules' => true,
    ],
];
