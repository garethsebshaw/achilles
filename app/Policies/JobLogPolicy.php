<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesTenantScopedLogs;

class JobLogPolicy
{
    use AuthorizesTenantScopedLogs;
}
