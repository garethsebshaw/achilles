<?php

// ✅ Enable Full Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ Load Composer Autoload
require __DIR__ . '/../vendor/autoload.php'; // Adjust path if needed

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

// ✅ Manually Define Database Configuration (Instead of `config/database.php`)
$dbConfig = [
    'driver'    => 'mysql', // Change if using SQLite or PostgreSQL
    'host'      => '127.0.0.1', // Change to your database host
    'database'  => 'achillesworkouts_laravel',
    'username'  => 'achillesworkouts_lardbuser',
    'password'  => 'dyr8peq!bxn8kbt6JNR',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
];

// ✅ Initialize Eloquent Database Capsule
$db = new DB;
$db->addConnection($dbConfig);
$db->setEventDispatcher(new Dispatcher(new Container));
$db->setAsGlobal();
$db->bootEloquent();

// ✅ Check Database Connection
try {
    DB::connection()->getPdo();
    echo "✅ Database connection successful.\n";
} catch (Exception $e) {
    die("❌ Database connection failed: " . $e->getMessage() . "\n");
}

// ✅ Set up Logging
$logFile = __DIR__ . '/../storage/logs/guide_assignment.log';

// Ensure log directory exists
if (!file_exists(dirname($logFile))) {
    mkdir(dirname($logFile), 0777, true);
}

// Start logging
file_put_contents($logFile, "=== Guide Assignment Log ===\n", FILE_APPEND);

// ✅ Get all workout sessions
$sessions = DB::table('workout_sessions')->pluck('id');

$report = [];

foreach ($sessions as $session_id) {
    echo "Processing session ID: $session_id\n";
    file_put_contents($logFile, "Processing session ID: $session_id\n", FILE_APPEND);

    // ✅ Fetch all athletes for this session
    $athletes = DB::table('workout_signups')
        ->join('users', 'workout_signups.user_id', '=', 'users.id')
        ->where('workout_signups.workout_session_id', $session_id)
        ->where('users.is_athlete', 1)
        ->pluck('workout_signups.user_id');

    if ($athletes->isEmpty()) {
        echo "⚠️ No athletes found for session ID: $session_id\n";
        file_put_contents($logFile, "⚠️ No athletes found for session ID: $session_id\n", FILE_APPEND);
        continue;
    }

    // ✅ Fetch all guides for this session
    $guides = DB::table('workout_signups')
        ->join('users', 'workout_signups.user_id', '=', 'users.id')
        ->where('workout_signups.workout_session_id', $session_id)
        ->where('users.is_athlete', 0)
        ->pluck('workout_signups.id', 'workout_signups.user_id');

    if ($guides->isEmpty()) {
        echo "⚠️ No guides found for session ID: $session_id\n";
        file_put_contents($logFile, "⚠️ No guides found for session ID: $session_id\n", FILE_APPEND);
        continue;
    }

    // ✅ Shuffle guides randomly
    $shuffled_guides = $guides->shuffle();

    // ✅ Assign guides to athletes in a round-robin way
    $athlete_count = count($athletes);
    $i = 0;
    foreach ($shuffled_guides as $guide_id => $signup_id) {
        $assigned_athlete = $athletes[$i % $athlete_count];

        try {
            DB::table('workout_signups')
                ->where('id', $signup_id)
                ->update(['athlete_id' => $assigned_athlete]);

            echo "✅ Guide ID: $guide_id assigned to Athlete ID: $assigned_athlete in session $session_id\n";
            file_put_contents($logFile, "✅ Guide ID: $guide_id assigned to Athlete ID: $assigned_athlete in session $session_id\n", FILE_APPEND);
        } catch (Exception $e) {
            echo "❌ ERROR updating guide ID $guide_id: " . $e->getMessage() . "\n";
            file_put_contents($logFile, "❌ ERROR updating guide ID $guide_id: " . $e->getMessage() . "\n", FILE_APPEND);
        }

        $report[$session_id][$assigned_athlete][] = $guide_id;
        $i++;
    }
}

// ✅ Generate Summary Report
echo "\n=== Assignment Summary ===\n";
file_put_contents($logFile, "\n=== Assignment Summary ===\n", FILE_APPEND);

foreach ($report as $session_id => $assignments) {
    echo "📌 Session ID: $session_id\n";
    file_put_contents($logFile, "📌 Session ID: $session_id\n", FILE_APPEND);

    foreach ($assignments as $athlete_id => $guide_list) {
        $guide_count = count($guide_list);
        echo "   🏃‍♂️ Athlete ID: $athlete_id → Assigned $guide_count Guides\n";
        file_put_contents($logFile, "   🏃‍♂️ Athlete ID: $athlete_id → Assigned $guide_count Guides\n", FILE_APPEND);
    }
}

echo "\n✅ Assignment Process Completed. Check $logFile for full details.\n";
