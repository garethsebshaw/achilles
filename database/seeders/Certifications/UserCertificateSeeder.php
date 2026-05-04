<?php

namespace Database\Seeders\Certifications;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UserCertificateSeeder extends Seeder
{
    private const USER_CHUNK_SIZE = 2000;
    private const INSERT_CHUNK_SIZE = 1000;

    protected $certificationStatuses = [];

    public function run(): void
    {
        $this->command->info('User Certificate Seeder Started');

        $this->certificationStatuses = DB::table('system_statuses')
            ->where('system_module_id', function($query) {
                $query->select('id')
                    ->from('system_modules')
                    ->where('model_type', 'App\Models\Certification')
                    ->first();
            })
            ->pluck('id', 'name')
            ->toArray();

        $certifications = DB::table('certifications')->get();
        $processedUsers = 0;

        DB::table('users')
            ->select('id')
            ->orderBy('id')
            ->chunkById(self::USER_CHUNK_SIZE, function (Collection $users) use ($certifications, &$processedUsers) {
                $rows = [];

                foreach ($users as $user) {
                    $numCertificates = rand(0, 3);

                    for ($i = 0; $i < $numCertificates; $i++) {
                        $rows[] = $this->certificatePayload($user->id, $certifications->random());
                    }

                    $processedUsers++;
                }

                foreach (array_chunk($rows, self::INSERT_CHUNK_SIZE) as $chunk) {
                    DB::table('user_certifications')->insert($chunk);
                }

                if ($processedUsers % 10000 === 0) {
                    $this->command->info(sprintf('Processed %d users for certification seeding...', $processedUsers));
                }
            }, 'id');

        $this->command->info('User Certificate Seeder Completed');
    }

    protected function certificatePayload(int $userId, object $certification): array
    {
        $startDate = Carbon::now()->subMonths(rand(0, 36));
        $validityPeriod = $certification->validity_period ?? rand(12, 24);
        $expiryDate = $startDate->copy()->addMonths($validityPeriod);

        $isRenewal = (bool)rand(0, 1);

        if ($isRenewal) {
            $startDate = $expiryDate->copy()->addMonths(rand(1, 3));
            $expiryDate = $startDate->copy()->addMonths($validityPeriod);
        }

        $certName = rand(0, 1)
            ? $certification->name . ' - ' . ['Level', 'Version', 'Class'][rand(0, 2)] . ' ' . rand(1, 5)
            : null;

        $status = $this->determineStatus($startDate, $expiryDate);

        return [
            'user_id' => $userId,
            'certification_id' => $certification->id,
            'name' => $certName,
            'certified_at' => $startDate,
            'expires_at' => $expiryDate,
            'validity_period' => $validityPeriod,
            'description' => null,
            'file_path' => null,
            'file_type' => null,
            'uploaded_at' => $startDate,
            'system_status_id' => $status,
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function determineStatus($startDate, $expiryDate)
    {
        $now = Carbon::now();
        $twoMonthsFromNow = $now->copy()->addMonths(2);

        // 2% chance of being revoked regardless of dates
        if (rand(1, 100) <= 2) {
            return $this->certificationStatuses['Revoked'];
        }

        // 1% chance of being suspended regardless of dates
        if (rand(1, 100) <= 1) {
            return $this->certificationStatuses['Suspended'];
        }

        // If start date is within last month, 80% chance of pending/review
        if ($startDate->greaterThan($now->copy()->subMonth())) {
            return rand(1, 100) <= 80
                ? $this->certificationStatuses['Pending']
                : $this->certificationStatuses['Under Review'];
        }

        // If expired
        if ($expiryDate->lessThan($now)) {
            return $this->certificationStatuses['Expired'];
        }

        // If within 2 months of expiry
        if ($expiryDate->lessThan($twoMonthsFromNow)) {
            return $this->certificationStatuses['Renewal Required'];
        }

        // Otherwise active
        return $this->certificationStatuses['Active'];
    }
}
