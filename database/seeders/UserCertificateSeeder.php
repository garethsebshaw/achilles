<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Certification;

class UserCertificateSeeder extends Seeder
{
    protected $certificationStatuses = [];

    public function run()
    {
        $this->command->info('User Certificate Seeder Started');

        // Get certification statuses
        $this->certificationStatuses = DB::table('system_statuses')
            ->where('system_module_id', function($query) {
                $query->select('id')
                    ->from('system_modules')
                    ->where('model_type', 'App\Models\Certification')
                    ->first();
            })
            ->pluck('id', 'name')
            ->toArray();

        // Get all users
        $users = DB::table('users')->pluck('id')->toArray();

        // Get all certifications
        $certifications = DB::table('certifications')->get();

        // Select 35% of users randomly
        //$selectedUsers = array_rand(array_flip($users), (int)(count($users) * 0.35));
        $selectedUsers = $users; // all users get at least 1 certificate
        $userCount = 0;

        foreach ($selectedUsers as $userId) {
            // Give each selected user 1-3 certificates
            $numCertificates = rand(0, 3);

            for ($i = 0; $i < $numCertificates; $i++) {
                $this->createCertificateForUser($userId, $certifications->random());
            }
            $userCount +=1;
            // Progress reporting every 100 users
            if ($userCount % 100 === 0) {
                echo "Processed user ID: $userCount\n";
            }
        }

        $this->command->info('User Certificate Seeder Completed');
    }

    protected function createCertificateForUser($userId, $certification)
    {
        // Generate random dates
        $startDate = Carbon::now()->subMonths(rand(0, 36)); // Random start date within past 3 years
        $validityPeriod = $certification->validity_period ?? rand(12, 24); // Use certification period or random
        $expiryDate = $startDate->copy()->addMonths($validityPeriod);

        // Determine if this is a renewal
        $isRenewal = (bool)rand(0, 1);
        if ($isRenewal) {
            // Adjust start date to be 1-3 months after original expiry
            $startDate = $expiryDate->copy()->addMonths(rand(1, 3));
            $expiryDate = $startDate->copy()->addMonths($validityPeriod);
        }

        // Determine certificate name (50% chance of custom name)
        $certName = rand(0, 1)
            ? $certification->name . ' - ' . ['Level', 'Version', 'Class'][rand(0, 2)] . ' ' . rand(1, 5)
            : null;

        // Determine status based on dates and random factors
        $status = $this->determineStatus($startDate, $expiryDate);

        try {
            DB::table('user_certifications')->insert([
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
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            $this->command->error("Error creating certificate for user {$userId}: {$e->getMessage()}");
        }
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
