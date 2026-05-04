<?php

namespace Database\Seeders\Certifications;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CertificationType;

class CertificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            // Training & Coaching
            ['name' => 'Training Skills', 'description' => 'Certifications for coaching and training abilities'],
            ['name' => 'First Aid & Safety', 'description' => 'Medical and safety certifications'],
            ['name' => 'Equipment Operation', 'description' => 'Equipment handling and operation certifications'],

            // Sports Specific
            ['name' => 'Running Coach', 'description' => 'Running and marathon coaching certifications'],
            ['name' => 'Cycling Coach', 'description' => 'Cycling and handcycling coaching certifications'],
            ['name' => 'Swimming Coach', 'description' => 'Swimming and water safety certifications'],
            ['name' => 'Adaptive Sports', 'description' => 'Specialized adaptive sports certifications'],
            ['name' => 'Winter Sports', 'description' => 'Skiing, snowboarding and winter sports certifications'],

            // Medical & Safety
            ['name' => 'CPR/AED', 'description' => 'Cardiopulmonary resuscitation and AED certifications'],
            ['name' => 'Emergency Response', 'description' => 'Emergency medical response certifications'],
            ['name' => 'Patient Care', 'description' => 'Patient handling and care certifications'],

            // Professional
            ['name' => 'Leadership', 'description' => 'Leadership and management certifications'],
            ['name' => 'Event Management', 'description' => 'Event planning and management certifications'],
            ['name' => 'Volunteer Management', 'description' => 'Volunteer coordination certifications'],

            // Equipment
            ['name' => 'Bike Mechanic', 'description' => 'Bicycle maintenance and repair certifications'],
            ['name' => 'Adaptive Equipment', 'description' => 'Adaptive equipment maintenance certifications'],
            ['name' => 'Safety Equipment', 'description' => 'Safety gear inspection and maintenance'],
        ];

        foreach ($types as $type) {
            CertificationType::create($type);
        }
//        $this->command->info(class_basename(static::class) . ' seeded successfully!');
    }
}
