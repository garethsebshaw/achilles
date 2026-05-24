<?php

return [
    'enabled' => env('RUNTIME_BRIDGE_ENABLED', true),
    'consumer' => env('RUNTIME_BRIDGE_CONSUMER', 'codex'),
    'shared_secret' => env('RUNTIME_BRIDGE_SHARED_SECRET'),
    'max_skew_seconds' => (int) env('RUNTIME_BRIDGE_MAX_SKEW_SECONDS', 300),
    'max_log_limit' => (int) env('RUNTIME_BRIDGE_MAX_LOG_LIMIT', 100),
];
