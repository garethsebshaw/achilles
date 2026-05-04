<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $overrides = [
            'Bronx' => [40.8448, -73.8648],
            'Brooklyn' => [40.6782, -73.9442],
            'Long Island' => [40.7891, -73.1350],
            'Manhattan' => [40.7831, -73.9712],
            'Queens' => [40.7282, -73.7949],
            'Staten Island' => [40.5795, -74.1502],
        ];

        foreach ($overrides as $city => [$latitude, $longitude]) {
            DB::table('system_locations')
                ->where('city', $city)
                ->where('state', 'NY')
                ->update([
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Intentionally irreversible. The previous coordinates were synthetic seed values.
    }
};
