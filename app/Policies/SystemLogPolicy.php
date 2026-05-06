<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesTenantScopedLogs;

class SystemLogPolicy
{
    use AuthorizesTenantScopedLogs;
}
