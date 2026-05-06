<?php

namespace Database\Seeders\Logging;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultTenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->firstOrCreate(
            ['key' => config('rsc_logging.default_tenant_key', 'achilles')],
            ['name' => 'Achilles']
        );

        User::query()
            ->whereNull('tenant_id')
            ->update(['tenant_id' => $tenant->id]);
    }
}
