<?php

namespace Database\Seeders\Geography;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemRegion;

class SystemRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            ['name' => 'North America', 'code' => 'NA', 'active' => 1],
            ['name' => 'South America', 'code' => 'SA', 'active' => 1],
            ['name' => 'Europe', 'code' => 'EU', 'active' => 1],
            ['name' => 'Asia Pacific', 'code' => 'APAC', 'active' => 1],
            ['name' => 'Africa', 'code' => 'AF', 'active' => 1],
            ['code' => 'AL', 'name' => 'Alabama', 'active' => 0],
            ['code' => 'AK', 'name' => 'Alaska', 'active' => 0],
            ['code' => 'AZ', 'name' => 'Arizona', 'active' => 1],
            ['code' => 'AR', 'name' => 'Arkansas', 'active' => 1],
            ['code' => 'CA', 'name' => 'California', 'active' => 1],
            ['code' => 'CO', 'name' => 'Colorado', 'active' => 1],
            ['code' => 'CT', 'name' => 'Connecticut', 'active' => 1],
            ['code' => 'DE', 'name' => 'Delaware', 'active' => 0],
            ['code' => 'FL', 'name' => 'Florida', 'active' => 0],
            ['code' => 'GA', 'name' => 'Georgia', 'active' => 0],
            ['code' => 'HI', 'name' => 'Hawaii', 'active' => 0],
            ['code' => 'ID', 'name' => 'Idaho', 'active' => 0],
            ['code' => 'IL', 'name' => 'Illinois', 'active' => 1],
            ['code' => 'IN', 'name' => 'Indiana', 'active' => 0],
            ['code' => 'IA', 'name' => 'Iowa', 'active' => 0],
            ['code' => 'KS', 'name' => 'Kansas', 'active' => 0],
            ['code' => 'KY', 'name' => 'Kentucky', 'active' => 0],
            ['code' => 'LA', 'name' => 'Louisiana', 'active' => 0],
            ['code' => 'ME', 'name' => 'Maine', 'active' => 0],
            ['code' => 'MD', 'name' => 'Maryland', 'active' => 0],
            ['code' => 'MA', 'name' => 'Massachusetts', 'active' => 1],
            ['code' => 'MI', 'name' => 'Michigan', 'active' => 0],
            ['code' => 'MN', 'name' => 'Minnesota', 'active' => 1],
            ['code' => 'MS', 'name' => 'Mississippi', 'active' => 0],
            ['code' => 'MO', 'name' => 'Missouri', 'active' => 1],
            ['code' => 'MT', 'name' => 'Montana', 'active' => 0],
            ['code' => 'NE', 'name' => 'Nebraska', 'active' => 0],
            ['code' => 'NV', 'name' => 'Nevada', 'active' => 1],
            ['code' => 'NH', 'name' => 'New Hampshire', 'active' => 0],
            ['code' => 'NJ', 'name' => 'New Jersey', 'active' => 1],
            ['code' => 'NM', 'name' => 'New Mexico', 'active' => 0],
            ['code' => 'NY', 'name' => 'New York', 'active' => 1],
            ['code' => 'NC', 'name' => 'North Carolina', 'active' => 1],
            ['code' => 'ND', 'name' => 'North Dakota', 'active' => 0],
            ['code' => 'OH', 'name' => 'Ohio', 'active' => 0],
            ['code' => 'OK', 'name' => 'Oklahoma', 'active' => 0],
            ['code' => 'OR', 'name' => 'Oregon', 'active' => 0],
            ['code' => 'PA', 'name' => 'Pennsylvania', 'active' => 1],
            ['code' => 'RI', 'name' => 'Rhode Island', 'active' => 0],
            ['code' => 'SC', 'name' => 'South Carolina', 'active' => 0],
            ['code' => 'SD', 'name' => 'South Dakota', 'active' => 0],
            ['code' => 'TN', 'name' => 'Tennessee', 'active' => 1],
            ['code' => 'TX', 'name' => 'Texas', 'active' => 1],
            ['code' => 'UT', 'name' => 'Utah', 'active' => 1],
            ['code' => 'VT', 'name' => 'Vermont', 'active' => 0],
            ['code' => 'VA', 'name' => 'Virginia', 'active' => 0],
            ['code' => 'WA', 'name' => 'Washington', 'active' => 0],
            ['code' => 'WV', 'name' => 'West Virginia', 'active' => 0],
            ['code' => 'WI', 'name' => 'Wisconsin', 'active' => 0],
            ['code' => 'WY', 'name' => 'Wyoming', 'active' => 0]
        ];

        foreach ($regions as $region) {
            SystemRegion::create($region);
        }
//        $this->command->info(class_basename(static::class) . ' seeded successfully!');
    }
}
