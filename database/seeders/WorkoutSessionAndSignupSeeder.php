<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory;

use App\Models\SystemModule;
use App\Models\SystemCategory;
use App\Models\SystemStatus;
use App\Models\SystemChapter;
use App\Models\SystemLocation;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use App\Models\WorkoutSpecificDetails;
use App\Models\SystemCountry;
use App\Models\SystemRegion;

class WorkoutSessionAndSignupSeeder extends Seeder
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function run()
    {
        // Get required data from existing tables
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->value('id');

        $nyChapter = $manhattanChapter = SystemChapter::where('name', 'Manhattan Achilles')->first();
        $brooklynChapter = SystemChapter::where('name', 'Brooklyn Achilles')->first();
        $longIslandChapter = SystemChapter::where('name', 'Long Island Achilles')->first();
        $bronxChapter = SystemChapter::where('name', 'Bronx Achilles')->first();
        $queensChapter = SystemChapter::where('name', 'Queens Achilles')->first();
        $statenIslandChapter = SystemChapter::where('name', 'Staten Island Achilles')->first();

        $sportCategories = SystemCategory::where('system_module_id', $workoutsModuleId)->get();
        $workoutStatuses = SystemStatus::where('system_module_id', $workoutsModuleId)->get();
        $signupStatuses = SystemStatus::where('system_module_id', $workoutsModuleId)->get();

        // Create workout locations primarily in New York
        $locations = $this->createWorkoutLocations($nyChapter);

        // Create workout templates
        $workoutTemplates = $this->createWorkoutTemplates($locations, $sportCategories);

        // Create workout sessions
        $workoutSessions = $this->createWorkoutSessions(
            $workoutTemplates,
            $locations,
            $workoutStatuses
        );

        // Get active users
        $users = User::where('is_subscribed', true)
            ->inRandomOrder()
            ->limit(20000)
            ->get();


        // Assign signups
        $this->assignWorkoutSignups(
            $workoutSessions,
            $users,
            $sportCategories,
            $signupStatuses
        );
    }

    private function createWorkoutLocations($nyChapter)

    {
        // Fetch the US country and Northeast region
        $usCountry = SystemCountry::where('name', 'United States')->first();
        $northeastRegion = SystemRegion::where('name', 'New York')->first();

        $locations = [
            [
                'name' => 'Prospect Park Running Area',
                'address_line_1' => 'Prospect Park',
                'address_line_2' => 'Near Grand Army Plaza',
                'city' => 'Brooklyn',
                'state' => 'NY',
                'postal_code' => '11225',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $brooklynChapter->id,
                'latitude' => 40.6602,
                'longitude' => -73.9690,
                'is_active' => true,
                'phone' => null,
                'email' => 'brooklynny@achillesinternational.org',
                'contact_name' => 'Brooklyn Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Popular running and walking location'
            ],

            [
                'name' => 'Eisenhower Park Running Area',
                'address_line_1' => 'Eisenhower Park',
                'address_line_2' => '1899 Hempstead Turnpike',
                'city' => 'East Meadow',
                'state' => 'NY',
                'postal_code' => '11554',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $longIslandChapter->id,
                'latitude' => 40.7229,
                'longitude' => -73.5715,
                'is_active' => true,
                'phone' => null,
                'email' => 'longislandachilles@gmail.com',
                'contact_name' => 'Long Island Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Popular running and walking location'
            ],

            [
                'name' => 'Van Cortlandt Park Running Area',
                'address_line_1' => 'Van Cortlandt Park',
                'address_line_2' => 'Broadway & 242nd Street',
                'city' => 'Bronx',
                'state' => 'NY',
                'postal_code' => '10471',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $bronxChapter->id,
                'latitude' => 40.8970,
                'longitude' => -73.8860,
                'is_active' => true,
                'phone' => null,
                'email' => 'BronxNY@achillesinternational.org',
                'contact_name' => 'Bronx Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Popular running and walking location'
            ],

            [
                'name' => 'Central Park Running Area',
                'address_line_1' => 'Central Park',
                'address_line_2' => 'Near 72nd Street',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10023',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $manhattanChapter->id,
                'latitude' => 40.7851,
                'longitude' => -73.9683,
                'is_active' => true,
                'phone' => null,
                'email' => 'fmagisano@achillesinternational.org',
                'contact_name' => 'Manhattan Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Popular running and walking location'
            ],

            [
                'name' => 'Flushing Meadows Running Area',
                'address_line_1' => 'Flushing Meadows Corona Park',
                'address_line_2' => 'Near Unisphere',
                'city' => 'Queens',
                'state' => 'NY',
                'postal_code' => '11368',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $queensChapter->id,
                'latitude' => 40.7498,
                'longitude' => -73.8408,
                'is_active' => true,
                'phone' => null,
                'email' => 'queensny@achillesinternational.org',
                'contact_name' => 'Queens Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Popular running and walking location'
            ],

            [
                'name' => 'Clove Lakes Park Running Area',
                'address_line_1' => 'Clove Lakes Park',
                'address_line_2' => '1150 Clove Rd',
                'city' => 'Staten Island',
                'state' => 'NY',
                'postal_code' => '10301',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $statenIslandChapter->id,
                'latitude' => 40.6140,
                'longitude' => -74.1050,
                'is_active' => true,
                'phone' => null,
                'email' => 'egulati@achillesinternational.org',
                'contact_name' => 'Staten Island Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Popular running and walking location'
            ],
            [
                'name' => 'St. John\'s Recreation Center',
                'address_line_1' => '1251 Prospect Pl',
                'address_line_2' => '',
                'city' => 'Brooklyn',
                'state' => 'NY',
                'postal_code' => '11213',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $brooklynChapter->id,
                'latitude' => 40.6714,
                'longitude' => -73.9336,
                'is_active' => true,
                'phone' => null,
                'email' => 'brooklynny@achillesinternational.org',
                'contact_name' => 'Brooklyn Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Indoor swimming and wheelchair basketball facility'
            ],

            [
                'name' => 'Greenbelt Recreation Center',
                'address_line_1' => '501 Brielle Ave',
                'address_line_2' => '',
                'city' => 'Staten Island',
                'state' => 'NY',
                'postal_code' => '10314',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $statenIslandChapter->id,
                'latitude' => 40.5903,
                'longitude' => -74.1343,
                'is_active' => true,
                'phone' => null,
                'email' => 'egulati@achillesinternational.org',
                'contact_name' => 'Staten Island Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Indoor recreation facility'
            ],

            [
                'name' => 'Brooklyn Boulders',
                'address_line_1' => '575 Degraw St',
                'address_line_2' => '',
                'city' => 'Brooklyn',
                'state' => 'NY',
                'postal_code' => '11217',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $brooklynChapter->id,
                'latitude' => 40.6795,
                'longitude' => -73.9833,
                'is_active' => true,
                'phone' => null,
                'email' => 'brooklynny@achillesinternational.org',
                'contact_name' => 'Brooklyn Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Indoor rock climbing facility'
            ],

            [
                'name' => 'Nassau County Aquatic Center',
                'address_line_1' => 'Merrick Ave',
                'address_line_2' => '',
                'city' => 'East Meadow',
                'state' => 'NY',
                'postal_code' => '11554',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $longIslandChapter->id,
                'latitude' => 40.7262,
                'longitude' => -73.5894,
                'is_active' => true,
                'phone' => null,
                'email' => 'longislandachilles@gmail.com',
                'contact_name' => 'Long Island Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Indoor swimming facility'
            ],

            [
                'name' => 'Al Oerter Recreation Center',
                'address_line_1' => '131-40 Fowler Ave',
                'address_line_2' => '',
                'city' => 'Flushing',
                'state' => 'NY',
                'postal_code' => '11355',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $queensChapter->id,
                'latitude' => 40.7515,
                'longitude' => -73.8322,
                'is_active' => true,
                'phone' => null,
                'email' => 'queensny@achillesinternational.org',
                'contact_name' => 'Queens Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Wheelchair basketball facility'
            ],

            [
                'name' => 'St. Mary\'s Recreation Center',
                'address_line_1' => '450 St Ann\'s Ave',
                'address_line_2' => '',
                'city' => 'Bronx',
                'state' => 'NY',
                'postal_code' => '10455',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $bronxChapter->id,
                'latitude' => 40.8100,
                'longitude' => -73.9170,
                'is_active' => true,
                'phone' => null,
                'email' => 'BronxNY@achillesinternational.org',
                'contact_name' => 'Bronx Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Indoor swimming and wheelchair basketball facility'
            ],

            [
                'name' => 'Mid-Island Y JCC',
                'address_line_1' => '45 Manetto Hill Rd',
                'address_line_2' => '',
                'city' => 'Plainview',
                'state' => 'NY',
                'postal_code' => '11803',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $longIslandChapter->id,
                'latitude' => 40.7806,
                'longitude' => -73.4745,
                'is_active' => true,
                'phone' => null,
                'email' => 'longislandachilles@gmail.com',
                'contact_name' => 'Long Island Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Indoor wheelchair basketball facility'
            ],

            [
                'name' => 'Flushing Meadows Corona Park Aquatic Center',
                'address_line_1' => '131-04 Meridian Rd',
                'address_line_2' => '',
                'city' => 'Queens',
                'state' => 'NY',
                'postal_code' => '11368',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $queensChapter->id,
                'latitude' => 40.7494,
                'longitude' => -73.8445,
                'is_active' => true,
                'phone' => null,
                'email' => 'queensny@achillesinternational.org',
                'contact_name' => 'Queens Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Indoor swimming facility'
            ],

            [
                'name' => 'Asser Levy Recreation Center',
                'address_line_1' => '392 Asser Levy Pl',
                'address_line_2' => '',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10010',
                'country_id' => $usCountry->id,
                'region_id' => $northeastRegion->id,
                'chapter_id' => $manhattanChapter->id,
                'latitude' => 40.7380,
                'longitude' => -73.9746,
                'is_active' => true,
                'phone' => null,
                'email' => 'fmagisano@achillesinternational.org',
                'contact_name' => 'Manhattan Chapter Coordinator',
                'timezone' => 'America/New_York',
                'notes' => 'Indoor swimming and wheelchair basketball facility'
            ],

        ];

        return collect($locations)->map(function ($locationData) {
            return SystemLocation::create($locationData);
        });
    }

    private function createWorkoutTemplates($locations, $sportCategories)
    {
        $templates = [];
        foreach ($sportCategories as $sport) {
            $templates[] = Workout::create([
                'location_id' => $locations->random()->id,
                'activity_type_id' => $sport->id,
                'name' => "{$sport->name} Workout Template",
                'default_start_time' => $this->faker->time('H:i'),
                'default_end_time' => $this->faker->time('H:i'),
                'is_recurring' => $this->faker->boolean(70),
                'created_by' => User::inRandomOrder()->first()->id,
                'is_template' => true
            ]);
        }
        return collect($templates);
    }

    private function createWorkoutSessions($workoutTemplates, $locations, $statuses)
    {
        $sessions = [];
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now()->addMonths(3);

        while ($startDate->lte($endDate)) {
            $dailySessionCount = $this->faker->numberBetween(1, 3);

            for ($i = 0; $i < $dailySessionCount; $i++) {
                $template = $workoutTemplates->random();
                $sessions[] = WorkoutSession::create([
                    'workout_id' => $template->id,
                    'location_id' => $locations->random()->id,
                    'session_date' => $startDate->copy(),
                    'start_time' => $template->default_start_time,
                    'end_time' => $template->default_end_time,
                    'status_id' => $statuses->random()->id,
                    'max_athletes' => $this->faker->numberBetween(10, 50),
                    'max_guides' => $this->faker->numberBetween(5, 20)
                ]);
            }

            $startDate->addDay();
        }

        return collect($sessions);
    }

    private function assignWorkoutSignups($workoutSessions, $users, $sportCategories, $statuses)
    {
        foreach ($workoutSessions as $session) {
            $signupCount = $this->faker->numberBetween(10, min(500, $users->count()));
            $sessionUsers = $users->random($signupCount);

            foreach ($sessionUsers as $user) {
                $signup = WorkoutSignup::create([
                    'workout_session_id' => $session->id,
                    'user_id' => $user->id,
                    'status_id' => $statuses->random()->id
                ]);

                // Add specific details based on sport
                WorkoutSpecificDetails::create([
                    'workout_signup_id' => $signup->id,
                    'sport_category_id' => $session->workout->activity_type_id,
                    'distance_unit_id' => 1,
                    'pace_unit_id' => 1,
                    'speed_unit_id' => 1,
                    'distance' => $this->faker->randomFloat(2, 1, 26.2),
                    'pace_min' => $this->faker->randomFloat(2, 8, 12),
                    'pace_max' => $this->faker->randomFloat(2, 8, 12)
                ]);
            }
        }
    }
}
