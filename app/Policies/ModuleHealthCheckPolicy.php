<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesTenantScopedLogs;

class ModuleHealthCheckPolicy
{
    use AuthorizesTenantScopedLogs;
}
