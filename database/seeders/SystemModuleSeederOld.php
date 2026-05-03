<?php

namespace Database\Seeders;

use App\Models\SystemModule;
use Illuminate\Database\Seeder;

class SystemModuleSeederOld extends Seeder
{
    protected $modules = [
        [
            'name' => 'Certifications',
            'model_type' => 'App\Models\Certification',
            'description' => 'Manage user certifications and qualifications',
            'active' => 1
        ],
        [
            'name' => 'Certification Documents',
            'model_type' => 'App\Models\CertificationDocument',
            'description' => 'Store and manage certification related documents',
            'active' => 1
        ],
        [
            'name' => 'Certification Types',
            'model_type' => 'App\Models\CertificationType',
            'description' => 'Define different types of certifications',
            'active' => 1
        ],
        [
            'name' => 'System Audit Logs',
            'model_type' => 'App\Models\SystemAuditLog',
            'description' => 'Track system-wide changes and actions',
            'active' => 1
        ],
        [
            'name' => 'Chapters',
            'model_type' => 'App\Models\SystemChapter',
            'description' => 'Manage organization chapters',
            'active' => 1
        ],
        [
            'name' => 'Chapter Contacts',
            'model_type' => 'App\Models\SystemChapterContact',
            'description' => 'Manage chapter contact information',
            'active' => 1
        ],
        [
            'name' => 'Countries',
            'model_type' => 'App\Models\SystemCountry',
            'description' => 'Manage country information',
            'active' => 1
        ],
        [
            'name' => 'Media Files',
            'model_type' => 'App\Models\SystemMediaFile',
            'description' => 'Manage system-wide media files',
            'active' => 1
        ],
        [
            'name' => 'System Modules',
            'model_type' => 'App\Models\SystemModule',
            'description' => 'Core system module management',
            'active' => 1
        ],
        [
            'name' => 'Notifications',
            'model_type' => 'App\Models\SystemNotification',
            'description' => 'System-wide notification management',
            'active' => 1
        ],
        [
            'name' => 'Regions',
            'model_type' => 'App\Models\SystemRegion',
            'description' => 'Manage geographical regions',
            'active' => 1
        ],
        [
            'name' => 'Settings',
            'model_type' => 'App\Models\SystemSetting',
            'description' => 'System-wide configuration settings',
            'active' => 1
        ],
        [
            'name' => 'Statuses',
            'model_type' => 'App\Models\SystemStatus',
            'description' => 'Manage status types across the system',
            'active' => 1
        ],
        [
            'name' => 'Tags',
            'model_type' => 'App\Models\SystemTag',
            'description' => 'System-wide tagging functionality',
            'active' => 1
        ],
        [
            'name' => 'Tag Relations',
            'model_type' => 'App\Models\SystemTaggable',
            'description' => 'Manage relationships between tags and other entities',
            'active' => 1
        ],
        [
            'name' => 'Users',
            'model_type' => 'App\Models\User',
            'description' => 'User management system',
            'active' => 1
        ],
        [
            'name' => 'User Certifications',
            'model_type' => 'App\Models\UserCertification',
            'description' => 'Manage user-specific certifications',
            'active' => 1
        ],
        [
            'name' => 'Equipment',
            'model_type' => 'App\Models\Equipment',
            'description' => 'Manage all equipment including bikes, skis, and adaptive equipment',
        ],
        [
            'name' => 'Equipment Maintenance',
            'model_type' => 'App\Models\EquipmentMaintenance',
            'description' => 'Track maintenance records and schedules for all equipment',
        ],
        [
            'name' => 'Equipment Maintenance Priority',
            'model_type' => 'App\Models\EquipmentMaintenancePriority',
            'description' => 'Manage priority levels for equipment maintenance tasks',
        ],
        [
            'name' => 'Guide Training',
            'model_type' => 'App\Models\GuideTraining',
            'description' => 'Manage guide training programs and certifications',
        ],
        [
            'name' => 'Workouts',
            'model_type' => 'App\Models\Workout',
            'description' => 'Base workout definitions and templates',
        ],
        [
            'name' => 'Workout Sessions',
            'model_type' => 'App\Models\WorkoutSession',
            'description' => 'Individual workout instances and occurrences',
        ],
        [
            'name' => 'Workout Equipment',
            'model_type' => 'App\Models\WorkoutEquipment',
            'description' => 'Workout Equipment',
        ],
        [
            'name' => 'Workout Locations',
            'model_type' => 'App\Models\WorkoutLocation',
            'description' => 'Manage workout locations and meeting points',
        ],
        [
            'name' => 'Workout Signups',
            'model_type' => 'App\Models\WorkoutSignup',
            'description' => 'User registrations for workouts',
        ],
        [
            'name' => 'Workout Attendance',
            'model_type' => 'App\Models\WorkoutAttendance',
            'description' => 'Track check-ins and attendance',
        ],
        [
            'name' => 'Workout Preferences',
            'model_type' => 'App\Models\WorkoutPreference',
            'description' => 'User preferences for different workout types',
        ],
        [
            'name' => 'Workout Groups',
            'model_type' => 'App\Models\WorkoutGroup',
            'description' => 'Manage workout groupings and pairings',
        ],
        [
            'name' => 'Workout Rules',
            'model_type' => 'App\Models\WorkoutRule',
            'description' => 'Define pairing and assignment rules',
        ],
        [
            'name' => 'Tandem Bike Pairings',
            'model_type' => 'App\Models\TandemBikeCompatibility',
            'description' => 'Guide + Athlete + Bike Compatability Pairings',
        ],
        [
            'name' => 'Workout Feedback',
            'model_type' => 'App\Models\WorkoutFeedback',
            'description' => 'Collect and manage workout feedback',
        ],
        [
            'name' => 'Weather Records',
            'model_type' => 'App\Models\WorkoutWeather',
            'description' => 'Track weather conditions for workouts',
        ],
        // Event System Modules
        [
            'name' => 'Events',
            'model_type' => 'App\Models\Event',
            'description' => 'Major events and competitions',
        ],
        [
            'name' => 'Event Registrations',
            'model_type' => 'App\Models\EventRegistration',
            'description' => 'Participant registrations for events',
        ],
        [
            'name' => 'Event Travel',
            'model_type' => 'App\Models\EventTravel',
            'description' => 'Travel and accommodation arrangements',
        ],
        [
            'name' => 'Event Equipment',
            'model_type' => 'App\Models\EventEquipment',
            'description' => 'Equipment assignments for events',
        ],
        [
            'name' => 'Event Teams',
            'model_type' => 'App\Models\EventTeam',
            'description' => 'Team management for relay events',
        ],
        [
            'name' => 'Event Documents',
            'model_type' => 'App\Models\EventDocument',
            'description' => 'Event-specific documentation and forms',
        ],
        [
            'name' => 'Event Results',
            'model_type' => 'App\Models\EventResult',
            'description' => 'Track event results and achievements',
        ],
        [
            'name' => 'Communications',
            'model_type' => 'App\Models\Communication',
            'description' => 'System-wide communication management',
        ],
        [
            'name' => 'Notifications',
            'model_type' => 'App\Models\Notification',
            'description' => 'User notifications and alerts',
        ],
        [
            'name' => 'Reports',
            'model_type' => 'App\Models\Report',
            'description' => 'Custom report generation',
        ],
        [
            'name' => 'Analytics',
            'model_type' => 'App\Models\Analytics',
            'description' => 'System analytics and metrics',
        ],
        // Volunteer Management
        [
            'name' => 'Volunteer Programs',
            'model_type' => 'App\Models\VolunteerProgram',
            'description' => 'Manage volunteer programs and opportunities',
        ],
        [
            'name' => 'Volunteer Hours',
            'model_type' => 'App\Models\VolunteerHours',
            'description' => 'Track volunteer time and contributions',
        ],
        [
            'name' => 'Volunteer Recognition',
            'model_type' => 'App\Models\VolunteerRecognition',
            'description' => 'Volunteer awards and recognition system',
        ],

// Safety/Incident Management
        [
            'name' => 'Safety Incidents',
            'model_type' => 'App\Models\SafetyIncident',
            'description' => 'Track and manage safety incidents',
        ],
        [
            'name' => 'Safety Reports',
            'model_type' => 'App\Models\SafetyReport',
            'description' => 'Safety reporting and analysis',
        ],
        [
            'name' => 'Emergency Contacts',
            'model_type' => 'App\Models\EmergencyContact',
            'description' => 'Emergency contact management',
        ],

// Transportation
        [
            'name' => 'Transportation',
            'model_type' => 'App\Models\Transportation',
            'description' => 'Manage transportation and logistics',
        ],
        [
            'name' => 'Vehicles',
            'model_type' => 'App\Models\Vehicle',
            'description' => 'Vehicle management and tracking',
        ],
        [
            'name' => 'Vehicle Maintenance',
            'model_type' => 'App\Models\VehicleMaintenance',
            'description' => 'Vehicle maintenance records',
        ],

// Awards and Achievements
        [
            'name' => 'Awards',
            'model_type' => 'App\Models\Award',
            'description' => 'Manage system awards and recognition',
        ],
        [
            'name' => 'Achievements',
            'model_type' => 'App\Models\Achievement',
            'description' => 'Track user achievements and milestones',
        ],
        [
            'name' => 'Award Ceremonies',
            'model_type' => 'App\Models\AwardCeremony',
            'description' => 'Manage award ceremonies and events',
        ],
        // Certification and Training
        [
            'name' => 'Certifications',
            'model_type' => 'App\Models\Certification',
            'description' => 'Manage user certifications and qualifications',
        ],
        [
            'name' => 'Guide Training',
            'model_type' => 'App\Models\GuideTraining',
            'description' => 'Manage guide training programs and certifications',
        ],
        // Equipment & Maintenance
        [
            'name' => 'Equipment',
            'model_type' => 'App\Models\Equipment',
            'description' => 'Manage all equipment including bikes, skis, and adaptive equipment',
        ],
        [
            'name' => 'Equipment Maintenance',
            'model_type' => 'App\Models\EquipmentMaintenance',
            'description' => 'Track maintenance records and schedules for all equipment',
        ],
        [
            'name' => 'Equipment Loan',
            'model_type' => 'App\Models\EquipmentLoan',
            'description' => 'Manage short-term and long-term equipment loans',
        ],
        [
            'name' => 'Equipment Customization',
            'model_type' => 'App\Models\EquipmentCustomization',
            'description' => 'Track customization requests and modifications for equipment',
        ],
        // Workouts & Fitness
        [
            'name' => 'Workouts',
            'model_type' => 'App\Models\Workout',
            'description' => 'Base workout definitions and templates',
        ],
        [
            'name' => 'Workout Sessions',
            'model_type' => 'App\Models\WorkoutSession',
            'description' => 'Individual workout instances and occurrences',
        ],
        [
            'name' => 'Workout Signups',
            'model_type' => 'App\Models\WorkoutSignup',
            'description' => 'User registrations for workouts',
        ],
        [
            'name' => 'Workout Feedback',
            'model_type' => 'App\Models\WorkoutFeedback',
            'description' => 'Collect and manage workout feedback',
        ],
        [
            'name' => 'Weather Records',
            'model_type' => 'App\Models\WorkoutWeather',
            'description' => 'Track weather conditions for workouts',
        ],
        // Event Management
        [
            'name' => 'Events',
            'model_type' => 'App\Models\Event',
            'description' => 'Major events and competitions',
        ],
        [
            'name' => 'Event Registrations',
            'model_type' => 'App\Models\EventRegistration',
            'description' => 'Participant registrations for events',
        ],
        [
            'name' => 'Event Travel',
            'model_type' => 'App\Models\EventTravel',
            'description' => 'Travel and accommodation arrangements',
        ],
        [
            'name' => 'Event Equipment',
            'model_type' => 'App\Models\EventEquipment',
            'description' => 'Equipment assignments for events',
        ],
        [
            'name' => 'Event Teams',
            'model_type' => 'App\Models\EventTeam',
            'description' => 'Team management for relay events',
        ],
        [
            'name' => 'Event Documents',
            'model_type' => 'App\Models\EventDocument',
            'description' => 'Event-specific documentation and forms',
        ],
        [
            'name' => 'Event Results',
            'model_type' => 'App\Models\EventResult',
            'description' => 'Track event results and achievements',
        ],
        // Volunteer Management
        [
            'name' => 'Volunteer Programs',
            'model_type' => 'App\Models\VolunteerProgram',
            'description' => 'Manage volunteer programs and opportunities',
        ],
        [
            'name' => 'Volunteer Hours',
            'model_type' => 'App\Models\VolunteerHours',
            'description' => 'Track volunteer time and contributions',
        ],
        [
            'name' => 'Volunteer Recognition',
            'model_type' => 'App\Models\VolunteerRecognition',
            'description' => 'Volunteer awards and recognition system',
        ],
        // Safety & Incident Reporting
        [
            'name' => 'Safety Incidents',
            'model_type' => 'App\Models\SafetyIncident',
            'description' => 'Track and manage safety incidents',
        ],
        [
            'name' => 'Safety Incident Severity',
            'model_type' => 'App\Models\SafetyIncidentSeverity',
            'description' => 'Categorize incidents by severity levels',
        ],
        [
            'name' => 'Emergency Contacts',
            'model_type' => 'App\Models\EmergencyContact',
            'description' => 'Emergency contact management',
        ],
        // Transportation & Vehicles
        [
            'name' => 'Transportation',
            'model_type' => 'App\Models\Transportation',
            'description' => 'Manage transportation and logistics',
        ],
        [
            'name' => 'Vehicles',
            'model_type' => 'App\Models\Vehicle',
            'description' => 'Vehicle management and tracking',
        ],
        [
            'name' => 'Vehicle Maintenance',
            'model_type' => 'App\Models\VehicleMaintenance',
            'description' => 'Vehicle maintenance records',
        ],
        // Achievements & Awards
        [
            'name' => 'Achievements',
            'model_type' => 'App\Models\Achievement',
            'description' => 'Track user achievements and milestones',
        ],
        [
            'name' => 'Award Ceremonies',
            'model_type' => 'App\Models\AwardCeremony',
            'description' => 'Manage award ceremonies and events',
        ],
        // Communication & Notifications
        [
            'name' => 'Communications',
            'model_type' => 'App\Models\Communication',
            'description' => 'System-wide communication management',
        ],
        [
            'name' => 'Notifications',
            'model_type' => 'App\Models\Notification',
            'description' => 'User notifications and alerts',
        ],
        // Performance & Reports
        [
            'name' => 'Reports',
            'model_type' => 'App\Models\Report',
            'description' => 'Custom report generation',
        ],
        [
            'name' => 'Analytics',
            'model_type' => 'App\Models\Analytics',
            'description' => 'System analytics and metrics',
        ],
        [
            'name' => 'Performance Evaluations',
            'model_type' => 'App\Models\PerformanceEvaluation',
            'description' => 'Monitor and review performance evaluations',
        ],
        // Financial & Administrative
        [
            'name' => 'Event Payments',
            'model_type' => 'App\Models\EventPayment',
            'description' => 'Manage event registration payments',
        ],
        [
            'name' => 'Event Accommodations',
            'model_type' => 'App\Models\EventAccommodation',
            'description' => 'Manage lodging and accommodations for events',
        ],
        [
            'name' => 'Sponsorships',
            'model_type' => 'App\Models\Sponsorship',
            'description' => 'Track and manage event sponsorships',
        ],
        // Quality & Moderation
        [
            'name' => 'Content Moderation',
            'model_type' => 'App\Models\ContentModeration',
            'description' => 'Moderate and manage content submissions',
        ],
        [
            'name' => 'Resource Allocation',
            'model_type' => 'App\Models\ResourceAllocation',
            'description' => 'Manage allocation of resources and equipment',
        ],
        [
            'name' => 'Qualification Verification',
            'model_type' => 'App\Models\QualificationVerification',
            'description' => 'Verify user qualifications and credentials',
        ],
        // Accessibility & Preferences
        [
            'name' => 'Accessibility Requirements',
            'model_type' => 'App\Models\AccessibilityRequirement',
            'description' => 'Manage accessibility preferences and accommodations',
        ],
        [
            'name' => 'Insurance Coverage',
            'model_type' => 'App\Models\InsuranceCoverage',
            'description' => 'Track and manage insurance coverage for participants and equipment',
        ],
        [
            'name' => 'Dietary Preferences',
            'model_type' => 'App\Models\DietaryPreference',
            'description' => 'Track and manage dietary needs and restrictions',
        ],
        [
            'name' => 'Languages Spoken',
            'model_type' => 'App\Models\LanguageProficiency',
            'description' => 'Manage the languages spoken by users and their proficiency levels',
        ],
        [
            'name' => 'Language Proficiency',
            'model_type' => 'App\Models\UserLanguage',
            'description' => 'Track user language skills and fluency levels',
        ],
        [
            // Equipment Fitting
            'name' => 'Equipment Fitting',
            'model_type' => 'App\Models\EquipmentFitting',
            'description' => 'Track equipment fitting states and requirements'
        ],
        [
            'name' => 'Assistance Requirements',
            'model_type' => 'App\Models\AssistanceRequirement',
            'description' => 'Manage support and assistance requirements'
        ],
        [
            'name' => 'Activity Risk',
            'model_type' => 'App\Models\ActivityRisk',
            'description' => 'Track and manage activity risk levels'
        ],
        [
            'name' => 'Guide Experience',
            'model_type' => 'App\Models\GuideExperience',
            'description' => 'Track guide experience levels and capabilities'
        ],
        [
            'name' => 'Equipment Condition',
            'model_type' => 'App\Models\EquipmentCondition',
            'description' => 'Manage equipment condition ratings'
        ],
        [
            'name' => 'Route Difficulty',
            'model_type' => 'App\Models\RouteDifficulty',
            'description' => 'Define and manage route difficulty levels'
        ]
    ];

    public function run()
    {
        foreach ($this->modules as $moduleData) {
            SystemModule::firstOrCreate(
                ['model_type' => $moduleData['model_type']],
                [
                    'name' => $moduleData['name'],
                    'description' => $moduleData['description'],
                    'active' => true,
                    'metadata' => null
                ]
            );
        }

        $this->command->info('System modules seeded successfully!');
    }
}
