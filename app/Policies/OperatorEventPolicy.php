<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesTenantScopedLogs;

class OperatorEventPolicy
{
    use AuthorizesTenantScopedLogs;
}
