<?php

namespace Database\Seeders\Logging;

use App\Modules\Logging\Services\ModuleHealthReporter;
use App\Modules\Logging\Services\OperatorEventLogger;
use App\Modules\Logging\Services\SystemLogger;
use Illuminate\Database\Seeder;

class LoggingDemoSeeder extends Seeder
{
    public function run(): void
    {
        app(SystemLogger::class)->info('logging.demo.info', 'Logging demo info event', [
            'module_key' => 'logging',
            'source_type' => 'seeder',
        ]);

        app(SystemLogger::class)->warning('logging.demo.warning', 'Logging demo warning event', [
            'module_key' => 'logging',
            'source_type' => 'seeder',
        ]);

        app(SystemLogger::class)->error('logging.demo.error', 'Logging demo error event', [
            'module_key' => 'logging',
            'source_type' => 'seeder',
        ]);

        app(\App\Modules\Logging\Services\JobLogger::class)->failed([
            'job_uuid' => 'demo-job-failure',
            'job_class' => 'Demo\\Logging\\FailedJob',
            'queue' => 'default',
            'connection' => 'database',
            'attempts' => 1,
            'exception_class' => \RuntimeException::class,
            'exception_message' => 'Demo failed job for logging visibility.',
            'trace_excerpt' => 'Demo trace excerpt',
        ]);

        app(ModuleHealthReporter::class)->warning('logging', 'Logging demo module health warning', [
            'source' => 'LoggingDemoSeeder',
        ]);

        app(OperatorEventLogger::class)->record(
            'logging.demo.operator-event',
            'Logging demo operator event',
            'Demo operator event created for local diagnostics.',
            ['module_key' => 'logging'],
            'notice',
            'open'
        );
    }
}
