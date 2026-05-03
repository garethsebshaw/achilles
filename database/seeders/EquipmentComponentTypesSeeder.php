<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemCategory;
use App\Models\ComponentType;

class EquipmentComponentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Bike',
            'Ski',
            'Snowboard',
            'Run',
            'Walk',
            'Swim',
            'Paraglide',
            'Parachute',
            'Kayak',
            'Canoe',
            'Raft',
            'Air Sports'
        ];

        $systemModuleId = \DB::table('system_modules')
            ->where('model_type', \App\Models\ComponentType::class)
            ->value('id');

        foreach ($categories as $mainCategory) {// Fetch the system_module_id for \App\Models\Language in one query

//            $this->command->info(class_basename(static::class) . $mainCategory . ' seeding!');

            // Fetch or create the main category
            $category = SystemCategory::firstOrCreate([
                'name' => $mainCategory,
                'system_module_id' => $systemModuleId
            ]);
//            $this->command->info(class_basename(static::class) . $mainCategory . "'s ID: " . $category->id);

            // empty the component Type
            $componentTypes = [];

            if ($mainCategory === 'Bike') {
//                $this->command->info(class_basename(static::class) . 'BIKING!');
                $componentTypes = [
                    // 🔹 **Frame & Structure**
                    ['name' => 'Frame', 'description' => 'The main body of the bike, supporting all components.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Rear Triangle', 'description' => 'The part of the frame connecting the rear wheel to the main frame.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Suspension Fork', 'description' => 'A front fork with shock absorbers for a smoother ride.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Suspension Seatpost', 'description' => 'A seatpost with built-in shock absorption for comfort.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],

                    // 🔹 **Wheels & Tires**
                    ['name' => 'Front Wheel', 'description' => 'The wheel at the front of the bike.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Rear Wheel', 'description' => 'The wheel at the back of the bike, typically with gears.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Front Left Wheel', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Front Right Wheel', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Rear Left Wheel', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Rear Right Wheel', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Tubeless Tire', 'description' => 'A tire without an inner tube, reducing puncture risks.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],
                    ['name' => 'Tubed Tire', 'description' => 'A traditional tire with an inner tube inside.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],
                    ['name' => 'Solid Tire', 'description' => 'A tire made of solid rubber, requiring no inflation.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Inner Tube', 'description' => 'A rubber tube inside the tire that holds air.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],

                    // 🔹 **Drivetrain & Gears**
                    ['name' => 'Front Derailleur', 'description' => 'Moves the chain between front gears for shifting.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Rear Derailleur', 'description' => 'Moves the chain between rear gears for shifting.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Chain', 'description' => 'Transfers power from the pedals to the wheels.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],
                    ['name' => 'Cassette', 'description' => 'A set of gears at the back of the bike for shifting.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Crankset', 'description' => 'The set of front gears connected to the pedals.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Bottom Bracket', 'description' => 'Connects the crankset to the frame, allowing it to spin.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],

                    // 🔹 **Brakes**
                    ['name' => 'Disc Brake - Mechanical', 'description' => 'Brake that uses a cable to stop the bike.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],
                    ['name' => 'Disc Brake - Hydraulic', 'description' => 'Brake that uses hydraulic fluid for better stopping power.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],
                    ['name' => 'Rim Brake - Caliper', 'description' => 'Brake that clamps onto the wheel rim to stop.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],
                    ['name' => 'Rim Brake - Cantilever', 'description' => 'Brake that clamps onto the wheel rim to stop.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],
                    ['name' => 'Drum Brake', 'description' => 'Brake housed within the wheel hub, often found on cargo bikes.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],
                    ['name' => 'Front Parking Brake', 'description' => 'Parking brake on front wheel.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],
                    ['name' => 'Rear Right Parking Brake', 'description' => 'Parking brake on front wheel.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],
                    ['name' => 'Rear Left Parking Brake', 'description' => 'Parking brake on front wheel.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 3],

                    // 🔹 **Cockpit & Steering**
                    ['name' => 'Handlebar', 'description' => 'The bar used for steering the bike.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Stem', 'description' => 'Connects the handlebar to the bike frame.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Grips', 'description' => 'Rubber or foam coverings on the handlebars for comfort.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Headset', 'description' => 'The bearings that allow the handlebars to turn smoothly.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],

                    // 🔹 **Seating**
                    ['name' => 'Saddle', 'description' => 'The seat of the bike.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Front Saddle (Tandem)', 'description' => 'The front seat on a tandem bike.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Rear Saddle (Tandem)', 'description' => 'The rear seat on a tandem bike.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Seat (Hand Cycle)', 'description' => 'A specialized seat for hand cycles.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],

                    // 🔹 **Pedals & Foot Supports**
                    ['name' => 'Pedals', 'description' => 'The parts where the rider places their feet to pedal.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Clipless Pedals', 'description' => 'Pedals that attach to cycling shoes for efficiency.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Toe Clips', 'description' => 'Straps that secure the foot to the pedal.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Hand Pedals (Hand Cycle)', 'description' => 'Hand-operated pedals for hand cycles.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],

                    // 🔹 **Hubs & Axles**
                    ['name' => 'Front Hub', 'description' => 'The center part of the front wheel that connects to the frame.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Rear Hub', 'description' => 'The center part of the rear wheel that houses the gears.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Axle', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],

                    // 🔹 **Accessories & Misc**
                    ['name' => 'Kickstand', 'description' => 'A stand to keep the bike upright when not in use.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Front Fender', 'description' => 'Guards to prevent water and mud from splashing.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Rear Fender', 'description' => 'Guards to prevent water and mud from splashing.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Bell', 'description' => 'A small bell to signal pedestrians or other riders.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],

                    // 🔹 **Lighting & Visibility**
                    ['name' => 'Front Light', 'description' => 'A headlight for visibility at night.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Rear Light', 'description' => 'A red taillight for safety.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Reflectors', 'description' => 'Reflective devices to improve visibility.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],

                    // 🔹 **Tracking & Electronics**
                    ['name' => 'GPS Unit', 'description' => 'A navigation device for tracking location.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Speed Sensor', 'description' => 'A sensor to measure bike speed.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Cadence Sensor', 'description' => 'A sensor to measure pedaling speed.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Power Meter', 'description' => 'A device that measures power output from pedaling.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],

                    // 🔹 **Storage & Carrying**
                    ['name' => 'Saddle Bag', 'description' => 'A small bag attached under the seat for carrying essentials.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Handlebar Bag', 'description' => 'A storage bag mounted on the handlebars.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Rear Rack', 'description' => 'A rack mounted on the back of the bike for carrying loads.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Panniers', 'description' => 'Bags that attach to the rear rack for extra storage.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Bottle Cage', 'description' => 'A holder for a water bottle on the bike frame.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],

                    // 🔹 **Security & Protection**
                    ['name' => 'Bike Lock', 'description' => 'A locking mechanism to prevent theft.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 36],
                    ['name' => 'Frame Protection Tape', 'description' => 'A tape used to protect the frame from scratches.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],

                    // 🔹 **Clothing & Accessories**
                    ['name' => 'Cycling Gloves', 'description' => 'Gloves that provide grip and protect hands.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Cycling Shoes', 'description' => 'Shoes designed for cycling with better grip and comfort.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Snowboarding Bag', 'description' => 'A bag to carry a snowboard and gear.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Skiing Bag', 'description' => 'A bag to carry skis and equipment.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],

                    // 🔹 **Training & Performance**
                    ['name' => 'Smart Trainer', 'description' => 'A stationary cycling trainer with resistance control.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Heart Rate Monitor', 'description' => 'A device that tracks heart rate during exercise.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Aerobars', 'description' => 'Bars that allow for a more aerodynamic riding position.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                ];
            } elseif ($mainCategory === 'Ski') {
//                $this->command->info(class_basename(static::class) . 'SKIING!');
                $componentTypes = [
                    ['name' => 'Skis', 'description' => 'Long, flat boards attached to boots for gliding over snow.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Bindings', 'description' => 'Mechanical devices that attach boots to skis and release in case of falls.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Poles', 'description' => 'Used for balance and propulsion while skiing.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Boots', 'description' => 'Specialized footwear that secures the foot and transfers movement to skis.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Helmet', 'description' => 'Protects the head from impacts and falls on the slopes.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Goggles', 'description' => 'Protect the eyes from wind, snow, and glare.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Ski Jacket', 'description' => 'Insulated and waterproof outerwear for warmth and protection.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Ski Pants', 'description' => 'Insulated and waterproof trousers for warmth and mobility.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Gloves', 'description' => 'Insulated gloves that provide grip and warmth.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Ski Bag', 'description' => 'Protective carrying case for skis and poles.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Avalanche Beacon', 'description' => 'Emergency transceiver for locating buried skiers.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Avalanche Probe', 'description' => 'Collapsible pole used to detect buried objects in snow.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Avalanche Shovel', 'description' => 'Compact shovel used for digging in snow emergencies.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                ];
            } elseif ($mainCategory === 'Snowboard') {
//                $this->command->info(class_basename(static::class) . 'SNOWBOARDING!');
                $componentTypes = [
                    ['name' => 'Snowboard', 'description' => 'A single wide board used for descending snow slopes.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Bindings', 'description' => 'Devices that attach boots to the snowboard.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Boots', 'description' => 'Supportive and insulated footwear designed for snowboarding.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Helmet', 'description' => 'Head protection to prevent injuries on slopes.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Goggles', 'description' => 'Eye protection against snow glare and wind.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Snowboard Jacket', 'description' => 'Waterproof and insulated jacket for warmth.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Snowboard Pants', 'description' => 'Waterproof and insulated pants for protection and mobility.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Snowboard Gloves', 'description' => 'Insulated gloves for grip and warmth.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Snowboard Bag', 'description' => 'A bag to carry and protect a snowboard.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Wrist Guards', 'description' => 'Protective gear to prevent wrist injuries.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                ];
            } elseif ($mainCategory === 'Run') {
//                $this->command->info(class_basename(static::class) . 'RUNNING!');
                $componentTypes = [
                    ['name' => 'Running Shoes', 'description' => 'Specialized shoes designed for running support and shock absorption.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 6],
                    ['name' => 'Orthotics', 'description' => 'Custom foot supports for better arch and foot alignment.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Prosthetic Blade', 'description' => 'A lightweight carbon-fiber leg for amputee runners.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Compression Socks', 'description' => 'Tight-fitting socks that improve circulation and reduce fatigue.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Hydration Pack', 'description' => 'A wearable pack with a built-in water reservoir.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Running Belt', 'description' => 'A belt for carrying small essentials like keys and energy gels.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Smartwatch / GPS Watch', 'description' => 'A wearable device for tracking pace, distance, and heart rate.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Headlamp', 'description' => 'A small, hands-free light for night running.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                ];
            } elseif ($mainCategory === 'Swim') {
//                $this->command->info(class_basename(static::class) . 'SWIMMING!');
                $componentTypes = [
                    ['name' => 'Swimsuit', 'description' => 'A streamlined suit designed for swimming performance.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Swim Cap', 'description' => 'A tight-fitting cap that reduces drag and keeps hair dry.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Goggles', 'description' => 'Protective eyewear that keeps water out and improves vision.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Kickboard', 'description' => 'A floating board used for swim training and leg workouts.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Hand Paddles', 'description' => 'Flat, plastic paddles that enhance upper body swim strength.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Pull Buoy', 'description' => 'A float used to isolate arm movement during swimming.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Swim Fins', 'description' => 'Rubber fins worn on feet to improve swimming speed and technique.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Wetsuit', 'description' => 'A neoprene suit that provides warmth in cold water.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Towel', 'description' => 'A soft absorbent cloth for drying off after swimming.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Ear Plugs', 'description' => 'Small plugs to prevent water from entering the ears.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Nose Clip', 'description' => 'A clip that prevents water from entering the nose.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Swim Bag', 'description' => 'A waterproof bag for carrying swim gear.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                ];
            } elseif ($mainCategory === 'Air Sports') {
//                $this->command->info(class_basename(static::class) . 'AIR SPORTS!');
                $componentTypes = [
                    ['name' => 'Parachute', 'description' => 'A fabric canopy used to slow descent during a jump.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Reserve Parachute', 'description' => 'A backup parachute in case the main one fails.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Harness', 'description' => 'A secure attachment system that connects the jumper to the parachute.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Helmet', 'description' => 'A protective helmet designed for air sports.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Goggles', 'description' => 'Eye protection against wind and debris.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Altimeter', 'description' => 'A device that measures altitude and descent speed.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Radio', 'description' => 'A communication device for coordination and safety.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Flight Suit', 'description' => 'An aerodynamic suit designed for air resistance control.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Wind Meter', 'description' => 'A device that measures wind speed for safe jumping.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Oxygen Mask', 'description' => 'A mask used for high-altitude jumps.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Skydiving Gloves', 'description' => 'Gloves that protect against cold and provide grip.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 12],
                    ['name' => 'Paraglider Wing', 'description' => 'A fabric wing that allows controlled descent and gliding.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                    ['name' => 'Paragliding Bag', 'description' => 'A storage bag for carrying and packing a paraglider.', 'system_category_id' => $category->id, 'default_maintenance_interval_months' => 24],
                ];
            } else {
//                $this->command->info(class_basename(static::class) . 'NOTHING FOUND!');
            }
        }

        // ✅ **Fix: Insert components with valid category_id**
        if (is_array($componentTypes) && count($componentTypes) > 0) {
//            $this->command->info(class_basename(static::class) . $mainCategory . '!');
            foreach ($componentTypes as $component) {
//                $this->command->info(class_basename(static::class) . $component['name'] . ' seeded successfully!');
                ComponentType::create($component);
            }
        }
    }
}
