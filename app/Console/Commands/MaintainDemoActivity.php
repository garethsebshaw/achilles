<?php

namespace App\Console\Commands;

use App\Support\DemoData\DemoUserRegistrationRedistributor;
use App\Support\DemoData\DemoWorkoutSignupWindowMaintainer;
use Illuminate\Console\Command;

class MaintainDemoActivity extends Command
{
    protected $signature = 'demo:simulate-activity
        {--redistribute-users : Redistribute existing user registrations across the history window}
        {--add-today-users : Create a small amount of new users for today}
        {--redistribute-signups : Redistribute existing workout signup timestamps}
        {--backfill-history : Top up historic sessions with demo signups}
        {--maintain-future : Top up future sessions inside the rolling window}
        {--history-days= : Number of days to shape historical data across}
        {--future-days= : Number of future days to keep filled}
        {--new-users-min= : Minimum new users to add for today}
        {--new-users-max= : Maximum new users to add for today}
        {--dry-run : Report intended changes without writing them}}';

    protected $description = 'Maintain randomized demo registrations and workout signup activity without resetting the database.';

    public function handle(
        DemoUserRegistrationRedistributor $registrations,
        DemoWorkoutSignupWindowMaintainer $signups,
    ): int {
        $historyDays = (int) ($this->option('history-days') ?: config('demo_activity.history_days', 365));
        $futureDays = (int) ($this->option('future-days') ?: config('demo_activity.future_days', 28));
        $newUsersMin = (int) ($this->option('new-users-min') ?: config('demo_activity.daily_new_users_min', 1));
        $newUsersMax = (int) ($this->option('new-users-max') ?: config('demo_activity.daily_new_users_max', 10));
        $dryRun = (bool) $this->option('dry-run');

        $runAll = ! $this->option('redistribute-users')
            && ! $this->option('add-today-users')
            && ! $this->option('redistribute-signups')
            && ! $this->option('backfill-history')
            && ! $this->option('maintain-future');

        if ($runAll || $this->option('redistribute-users')) {
            $result = $registrations->redistributeHistory($historyDays, $dryRun);
            $this->info(sprintf('Redistributed %d user registrations across %d days.', $result['users'], $result['days']));
        }

        if ($runAll || $this->option('add-today-users')) {
            $result = $registrations->createTodayUsers($newUsersMin, $newUsersMax, $dryRun);
            $this->info(sprintf('Created %d users for today.', $result['users']));
        }

        if ($runAll || $this->option('redistribute-signups')) {
            $result = $signups->redistributeExistingHistory($historyDays, $futureDays, $dryRun);
            $this->info(sprintf('Redistributed %d workout signups.', $result['updated_signups']));
        }

        if ($runAll || $this->option('backfill-history')) {
            $result = $signups->backfillHistoricSessions($historyDays, $dryRun);
            $this->info(sprintf(
                'Processed %d historic sessions and created %d signups.',
                $result['processed_sessions'],
                $result['created_signups']
            ));
        }

        if ($runAll || $this->option('maintain-future')) {
            $result = $signups->maintainUpcomingSessions($futureDays, $dryRun);
            $this->info(sprintf(
                'Processed %d upcoming sessions and created %d signups.',
                $result['processed_sessions'],
                $result['created_signups']
            ));
        }

        if ($dryRun) {
            $this->warn('Dry run only. No database changes were written.');
        }

        return self::SUCCESS;
    }
}
