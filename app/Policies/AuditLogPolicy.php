<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesTenantScopedLogs;

class AuditLogPolicy
{
    use AuthorizesTenantScopedLogs;
}
