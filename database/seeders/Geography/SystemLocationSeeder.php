<?php

namespace Database\Seeders\Geography;

use App\Models\SystemChapter;
use App\Models\SystemLocation;
use Illuminate\Database\Seeder;

class SystemLocationSeeder extends Seeder
{
    private const CITY_COORDINATE_OVERRIDES = [
        'Bronx' => [40.8448, -73.8648],
        'Brooklyn' => [40.6782, -73.9442],
        'Long Island' => [40.7891, -73.1350],
        'Manhattan' => [40.7831, -73.9712],
        'Queens' => [40.7282, -73.7949],
        'Staten Island' => [40.5795, -74.1502],
    ];

    private const COUNTRY_COORDINATES = [
        'AU' => [-25.2744, 133.7751],
        'BR' => [-14.2350, -51.9253],
        'CA' => [56.1304, -106.3468],
        'CO' => [4.5709, -74.2973],
        'DE' => [51.1657, 10.4515],
        'EC' => [-1.8312, -78.1834],
        'IT' => [41.8719, 12.5674],
        'JP' => [36.2048, 138.2529],
        'MN' => [46.8625, 103.8467],
        'MX' => [23.6345, -102.5528],
        'NO' => [60.4720, 8.4689],
        'NZ' => [-40.9006, 174.8860],
        'PA' => [8.5380, -80.7821],
        'PE' => [-9.1900, -75.0152],
        'RU' => [61.5240, 105.3188],
        'UK' => [55.3781, -3.4360],
        'US' => [39.8283, -98.5795],
        'ZA' => [-30.5595, 22.9375],
    ];

    private const COUNTRY_TIMEZONES = [
        'AU' => 'Australia/Sydney',
        'BR' => 'America/Sao_Paulo',
        'CA' => 'America/Toronto',
        'CO' => 'America/Bogota',
        'DE' => 'Europe/Berlin',
        'EC' => 'America/Guayaquil',
        'IT' => 'Europe/Rome',
        'JP' => 'Asia/Tokyo',
        'MN' => 'Asia/Ulaanbaatar',
        'MX' => 'America/Mexico_City',
        'NO' => 'Europe/Oslo',
        'NZ' => 'Pacific/Auckland',
        'PA' => 'America/Panama',
        'PE' => 'America/Lima',
        'RU' => 'Europe/Moscow',
        'UK' => 'Europe/London',
        'US' => 'America/New_York',
        'ZA' => 'Africa/Johannesburg',
    ];

    private const US_REGION_TIMEZONES = [
        'AR' => 'America/Chicago',
        'AZ' => 'America/Phoenix',
        'CA' => 'America/Los_Angeles',
        'CO' => 'America/Denver',
        'CT' => 'America/New_York',
        'IL' => 'America/Chicago',
        'MA' => 'America/New_York',
        'MN' => 'America/Chicago',
        'MO' => 'America/Chicago',
        'NC' => 'America/New_York',
        'NJ' => 'America/New_York',
        'NY' => 'America/New_York',
        'OH' => 'America/New_York',
        'PA' => 'America/New_York',
        'SC' => 'America/New_York',
        'TN' => 'America/Chicago',
        'TX' => 'America/Chicago',
        'VA' => 'America/New_York',
        'WA' => 'America/Los_Angeles',
        'WI' => 'America/Chicago',
    ];

    public function run(): void
    {
        $chapters = SystemChapter::with(['contacts', 'country', 'region'])
            ->where('active', true)
            ->orderBy('name')
            ->get();

        foreach ($chapters as $index => $chapter) {
            $country = $chapter->country;
            $region = $chapter->region;
            [$latitude, $longitude] = $this->coordinatesFor($chapter->city, $country?->iso2, $index);

            $city = $chapter->city ?: $chapter->name;
            $state = $chapter->state ?: ($country?->iso2 === 'US' ? $region?->code : $region?->name);

            SystemLocation::query()->create([
                'name' => sprintf('%s Primary Training Hub', $chapter->name),
                'address_line_1' => sprintf('%d Achilles Way', 100 + $chapter->id),
                'address_line_2' => sprintf('Suite %02d', ($chapter->id % 40) + 1),
                'city' => $city,
                'state' => $state,
                'postal_code' => $chapter->postal_code ?: sprintf('%05d', 10000 + $chapter->id),
                'country_id' => $chapter->system_country_id,
                'region_id' => $chapter->system_region_id,
                'chapter_id' => $chapter->id,
                'phone' => $chapter->phone,
                'email' => $chapter->email,
                'contact_name' => optional($chapter->contacts->first())->name,
                'timezone' => $this->timezoneFor($country?->iso2, $region?->code),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'is_active' => true,
                'notes' => 'Primary weekly training and meetup location seeded for every chapter.',
                'metadata' => [
                    'seeded' => true,
                    'supports_all_sports' => true,
                    'chapter_name' => $chapter->name,
                    'country_code' => $country?->iso2,
                ],
            ]);
        }
    }

    private function coordinatesFor(?string $city, ?string $countryCode, int $index): array
    {
        if ($city && isset(self::CITY_COORDINATE_OVERRIDES[$city])) {
            return self::CITY_COORDINATE_OVERRIDES[$city];
        }

        [$baseLatitude, $baseLongitude] = self::COUNTRY_COORDINATES[$countryCode] ?? [0.0, 0.0];

        $latitude = round($baseLatitude + ((($index % 7) - 3) * 0.18), 6);
        $longitude = round($baseLongitude + ((($index % 9) - 4) * 0.22), 6);

        return [$latitude, $longitude];
    }

    private function timezoneFor(?string $countryCode, ?string $regionCode): string
    {
        if ($countryCode === 'US' && $regionCode && isset(self::US_REGION_TIMEZONES[$regionCode])) {
            return self::US_REGION_TIMEZONES[$regionCode];
        }

        return self::COUNTRY_TIMEZONES[$countryCode] ?? 'UTC';
    }
}
