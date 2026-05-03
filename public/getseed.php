<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = '127.0.0.1';
$db = 'achillesworkouts_lar2';
$user = 'achillesworkouts_lardbuser';
$pass = 'dyr8peq!bxn8kbt6JNR';
$charset = 'utf8mb4';

// DSN for the connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

// Seed file generation
$output = "<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
    ";

$output .= "
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');";

$output .= "User::create([
            'email' => 'thisisg@gmail.com',
            'name' => 'Gareth Redfern-Shaw',
            'first_name' => 'Gareth',
            'middle_name' => 'Sebastian',
            'last_name' => 'Redfern-Shaw',
            'preferred_name' => 'Gareth',
            'password' => 'rcp@UHE-nmq_kmw0qzk',
            'picture' => 'https://s3.us-east-2.amazonaws.com/storage.r2.rosterfy.com/misc/0miv/0mivWeAQHRyRF3A0pxhLmaFYX3fHjqW1o40elK19.jpg',
            'is_sys_admin' => '1',
            'is_guide' => '1',
            'is_athlete' => '0',
            'is_team_leader' => '0',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);\n

        User::create([
            'email' => 'cfee@achillesinternational.org',
            'name' => 'Chandler Fee',
            'first_name' => 'Chandler',
            'middle_name' => null,
            'last_name' => 'Fee',
            'preferred_name' => 'Chandler',
            'password' => 'default_pa55word!',
            'picture' => 'https://fordhamsports.com/images/2016/10/17/DayHS2016.jpg',
            'is_sys_admin' => '1',
            'is_team_leader' => '1',
            'is_guide' => '1',
            'is_athlete' => '0',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);\n
        User::create([
            'email' => 'jday0210@gmail.com',
            'name' => 'Julia Day',
            'first_name' => 'Julia',
            'middle_name' => null,
            'last_name' => 'Day',
            'preferred_name' => 'Julia',
            'password' => 'EpicTriLady!',
            'picture' => 'https://fordhamsports.com/images/2016/10/17/DayHS2016.jpg',
            'is_sys_admin' => '1',
            'is_team_leader' => '1',
            'is_guide' => '1',
            'is_athlete' => '0',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);\n
        ";


// Query to get users from the users table
$query = "SELECT * FROM users WHERE id>1 AND id!=21430582 ORDER by id ASC";
$stmt = $pdo->query($query);

// Fetch all users
$users = $stmt->fetchAll();

foreach ($users as $user) {

    $output .= "        User::create([\n";
    $output .= "            'email' => '" . addslashes($user['email'] ?? '') . "',\n";
    $output .= "            'name' => '" . addslashes($user['name'] ?? '') . "',\n";
    $output .= "            'first_name' => '" . addslashes($user['first_name'] ?? '') . "',\n";
    $output .= "            'middle_name' => '" . addslashes($user['middle_name'] ?? '') . "',\n";
    $output .= "            'last_name' => '" . addslashes($user['last_name'] ?? '') . "',\n";
    $output .= "            'preferred_name' => '" . addslashes($user['preferred_name'] ?? '') . "',\n";
    $output .= "            'password' => 'default_password',\n"; // Replace 'default_password' as needed
    $output .= "            'picture' => '" . addslashes($user['picture'] ?? '') . "',\n";

    // ********
    // guide/athlete/admin logic
    // Check if email contains achillesinternational
    $isAchillesEmail = strpos(strtolower($user['email']), 'achillesinternational') !== false;

    if ($isAchillesEmail) {
        // Admin and guide with admin email
        $output .= "            'is_admin' => 1,\n";
        $output .= "            'is_athlete' => 0,\n";
        $output .= "            'is_guide' => 1,\n";
    } else {
        // Randomly choose athlete or guide
        $isAthlete = rand(0, 1);
        $output .= "            'is_admin' => 0,\n";
        $output .= "            'is_athlete' => " . $isAthlete . ",\n";
        $output .= "            'is_guide' => " . (1 - $isAthlete) . ",\n";
    }

    // Team leader for less than 5% of guides
    $isTeamLeader = ($isAchillesEmail || (rand(0, 100) < 3 && !$isAthlete)) ? 1 : 0;
    $output .= "            'is_team_leader' => " . $isTeamLeader . ",\n";
    // ********

    $output .= "            'email_verified_at' => '" . $user['created_at'] . "',\n";
    $output .= "            'created_at' => '" . $user['created_at'] . "',\n";
    $output .= "            'updated_at' => '" . $user['updated_at'] . "',\n";
    $output .= "        ]);\n";
}


$output .= "    }\n}\n";

$output .= "
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');";

// Save to a file
$file = 'UserSeeder.php';

// Delete the file if it already exists
if (file_exists($file)) {
    unlink($file);
}

file_put_contents($file, $output);

echo "Seeder file generated: $file\n" . date("h:i:sa");
