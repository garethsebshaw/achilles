<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\CertificationType;
use App\Models\SystemStatus;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatus = SystemStatus::where('code', 'cert_active')->first()->id;

        $certifications = [
            'Training Skills' => [
                ['name' => 'Basic Training Skills', 'validity_period' => 24],
                ['name' => 'Advanced Training Methods', 'validity_period' => 24],
                ['name' => 'Inclusive Training Certification', 'validity_period' => 24],
            ],

            'First Aid & Safety' => [
                ['name' => 'Basic First Aid', 'validity_period' => 24, 'requires_document' => true],
                ['name' => 'Advanced First Aid', 'validity_period' => 24, 'requires_document' => true],
                ['name' => 'CPR/AED', 'validity_period' => 12, 'requires_document' => true],
                ['name' => 'Adult and Pediatric First Aid/CPR/AED', 'validity_period' => 12, 'requires_document' => true],
                ['name' => 'Adult First Aid/CPR/AED Online', 'validity_period' => 12, 'requires_document' => true],
                ['name' => 'Adult CPR/AED Online', 'validity_period' => 12, 'requires_document' => true],
                ['name' => 'Child and Baby First Aid/CPR/AED Online', 'validity_period' => 12, 'requires_document' => true],
            ],

            'Equipment Operation' => [
                ['name' => 'Basic Equipment Safety', 'validity_period' => 12],
                ['name' => 'Advanced Equipment Operation', 'validity_period' => 24],
                ['name' => 'Safety Inspection', 'validity_period' => 12],
            ],

            'Endurance Coach' => [
                ['name' => 'UESCA Triathlon Coach Certification', 'validity_period' => 24, 'requires_document' => true],
                ['name' => 'UESCA Endurance Sports Nutrition Certification', 'validity_period' => 24, 'requires_document' => true],
                ['name' => 'UESCA Injury Prevention for the Endurance Athlete', 'validity_period' => 24],
            ],

            'Triathlon Coach' => [
                ['name' => 'UESCA Triathlon Coach Certification', 'validity_period' => 24, 'requires_document' => true],
            ],

            'Running Coach' => [
                ['name' => 'RRCA Level 1', 'validity_period' => 24, 'requires_document' => true],
                ['name' => 'RRCA Level 2', 'validity_period' => 24, 'requires_document' => true],
                ['name' => 'Marathon Training Specialist', 'validity_period' => 24],
                ['name' => 'UESCA Running Event Race Director', 'validity_period' => 24],
                ['name' => 'UESCA Ultrarunning Coach Certification', 'validity_period' => 24],
            ],

            'Cycling Coach' => [
                ['name' => 'Basic Cycling Coach', 'validity_period' => 24],
                ['name' => 'Handcycling Specialist', 'validity_period' => 24],
                ['name' => 'Advanced Cycling Coach', 'validity_period' => 24],
                ['name' => 'UESCA Cycling Coach Certification', 'validity_period' => 24],
            ],

            'Swimming Coach' => [
                ['name' => 'Water Safety Instructor', 'validity_period' => 24, 'requires_document' => true],
                ['name' => 'Adaptive Swimming Coach', 'validity_period' => 24],
                ['name' => 'Advanced Swim Coach', 'validity_period' => 24],
                ['name' => 'UESCA Freestyle Stroke Form and Correction', 'validity_period' => 24],
            ],

            'Adaptive Sports' => [
                ['name' => 'Adaptive Sports Basics', 'validity_period' => 24],
                ['name' => 'Paralympic Sport Coach', 'validity_period' => 24],
                ['name' => 'Adaptive Equipment Specialist', 'validity_period' => 24],
            ],

            'Winter Sports' => [
                ['name' => 'Adaptive Ski Instructor', 'validity_period' => 24],
                ['name' => 'Snow Sports Safety', 'validity_period' => 12],
                ['name' => 'Winter Paralympic Coach', 'validity_period' => 24],
            ],

            'Leadership' => [
                ['name' => 'Team Leadership', 'validity_period' => 36],
                ['name' => 'Program Management', 'validity_period' => 36],
                ['name' => 'Chapter Leadership', 'validity_period' => 36],
            ],

            'Event Management' => [
                ['name' => 'Event Planning', 'validity_period' => 36],
                ['name' => 'Race Director', 'validity_period' => 24],
                ['name' => 'Event Safety Management', 'validity_period' => 24],
            ],

            'Volunteer Management' => [
                ['name' => 'Volunteer Coordinator', 'validity_period' => 36],
                ['name' => 'Volunteer Training', 'validity_period' => 24],
                ['name' => 'Team Building', 'validity_period' => 36],
            ],

            'Bike Mechanic' => [
                ['name' => 'Basic Bike Maintenance', 'validity_period' => 24],
                ['name' => 'Advanced Bike Repair', 'validity_period' => 24],
                ['name' => 'Adaptive Bike Specialist', 'validity_period' => 24],
            ],

            'Adaptive Equipment' => [
                ['name' => 'Equipment Maintenance', 'validity_period' => 24],
                ['name' => 'Equipment Safety Inspector', 'validity_period' => 12],
                ['name' => 'Equipment Fitting Specialist', 'validity_period' => 24],
            ],
        ];

        foreach ($certifications as $typeName => $certs) {
            $type = CertificationType::where('name', $typeName)->first();
            if ($type) {
                foreach ($certs as $cert) {
                    Certification::create(array_merge($cert, [
                        'certification_type_id' => $type->id,
                        'system_status_id' => $activeStatus,
                        'description' => 'Certification for ' . $cert['name'],
                    ]));
                }
            }
        }
//        $this->command->info(class_basename(static::class) . ' seeded successfully!');
    }
}
