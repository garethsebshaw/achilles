<?php

namespace Database\Seeders;

use App\Models\SystemModule;
use Illuminate\Database\Seeder;

class SystemModuleSeeder extends Seeder
{
    /**
     * Module definitions organized by category
     * Removed duplicates and consolidated related functionality
     * System-wide tables are grouped at the top
     */
    protected $modules = [
        // System-wide Core Modules
        [
            'name' => 'System Modules',
            'model_type' => 'App\Models\SystemModule',
            'description' => 'Core system module management',
            'active' => 1
        ],
        [
            'name' => 'System Settings',
            'model_type' => 'App\Models\SystemSetting',
            'description' => 'System-wide configuration settings',
            'active' => 1
        ],
        [
            'name' => 'System Audit Logs',
            'model_type' => 'App\Models\SystemAuditLog',
            'description' => 'Track system-wide changes and actions',
            'active' => 1
        ],
        [
            'name' => 'Media Files',
            'model_type' => 'App\Models\SystemMediaFile',
            'description' => 'Manage system-wide media files',
            'active' => 1
        ],

        // System-wide Classification Tables
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
            'name' => 'Statuses',
            'model_type' => 'App\Models\SystemStatus',
            'description' => 'Manage status types across the system',
            'active' => 1
        ],

        // Location & Geography
        [
            'name' => 'Countries',
            'model_type' => 'App\Models\SystemCountry',
            'description' => 'Manage country information',
            'active' => 1
        ],
        [
            'name' => 'Regions',
            'model_type' => 'App\Models\SystemRegion',
            'description' => 'Manage geographical regions',
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

        // User Management & Profiles
        [
            'name' => 'Users',
            'model_type' => 'App\Models\User',
            'description' => 'User management system',
            'active' => 1
        ],
        [
            'name' => 'Emergency Contacts',
            'model_type' => 'App\Models\EmergencyContact',
            'description' => 'Emergency contact management',
            'active' => 0
        ],
        [
            'name' => 'Language Proficiency',
            'model_type' => 'App\Models\LanguageProficiency',
            'description' => 'Track user language skills and proficiency levels',
            'active' => 1
        ],
        [
            'name' => 'Languages',
            'model_type' => 'App\Models\Language',
            'description' => 'Language Management',
            'active' => 1
        ],
        [
            'name' => 'Dietary Preferences',
            'model_type' => 'App\Models\DietaryPreference',
            'description' => 'Track and manage dietary needs and restrictions',
            'active' => 0
        ],
        [
            'name' => 'Accessibility Requirements',
            'model_type' => 'App\Models\AccessibilityRequirement',
            'description' => 'Manage accessibility preferences and accommodations',
            'active' => 0
        ],

        // Certifications & Training
        [
            'name' => 'Certifications',
            'model_type' => 'App\Models\Certification',
            'description' => 'Manage user certifications and qualifications',
            'active' => 1
        ],
        [
            'name' => 'Certification Types',
            'model_type' => 'App\Models\CertificationType',
            'description' => 'Define different types of certifications',
            'active' => 1
        ],
        [
            'name' => 'Certification Documents',
            'model_type' => 'App\Models\CertificationDocument',
            'description' => 'Store and manage certification related documents',
            'active' => 1
        ],
        [
            'name' => 'Guide Training',
            'model_type' => 'App\Models\GuideTraining',
            'description' => 'Manage guide training programs and certifications',
            'active' => 0
        ],
        [
            'name' => 'Guide Experience',
            'model_type' => 'App\Models\GuideExperience',
            'description' => 'Track guide experience levels and capabilities',
            'active' => 0
        ],

        // Equipment Core
        [
            'name' => 'Equipment Management',
            'model_type' => 'App\Models\Equipment',
            'description' => 'Manage all types of equipment including bikes, skis, and adaptive equipment',
            'active' => 1
        ],
        [
            'name' => 'Equipment Components',
            'model_type' => 'App\Models\EquipmentComponent',
            'description' => 'Track individual components of equipment items',
            'active' => 1
        ],
        [
            'name' => 'Component Types',
            'model_type' => 'App\Models\ComponentType',
            'description' => 'Manage different types of equipment components',
            'active' => 1
        ],
        [
            'name' => 'Equipment Manufacturers',
            'model_type' => 'App\Models\Manufacturer',
            'description' => 'Manage equipment and component manufacturers',
            'active' => 1
        ],
        [
            'name' => 'Equipment Fitting',
            'model_type' => 'App\Models\EquipmentFitting',
            'description' => 'Track equipment fitting states and requirements',
            'active' => 0
        ],
        [
            'name' => 'Equipment Customization',
            'model_type' => 'App\Models\EquipmentCustomization',
            'description' => 'Track customization requests and modifications',
            'active' => 0
        ],

        // Equipment Conditions & Status
        [
            'name' => 'Equipment Conditions',
            'model_type' => 'App\Models\EquipmentCondition',
            'description' => 'Track and manage equipment condition states',
            'active' => 1
        ],
        [
            'name' => 'Equipment Maintenance',
            'model_type' => 'App\Models\EquipmentMaintenance',
            'description' => 'Manage equipment maintenance tasks',
            'active' => 1
        ],
        [
            'name' => 'Equipment Maintenance Priorities',
            'model_type' => 'App\Models\EquipmentMaintenancePriority',
            'description' => 'Manage priority levels for maintenance tasks',
            'active' => 1
        ],

        // Maintenance & Service
        [
            'name' => 'Maintenance Requests',
            'model_type' => 'App\Models\MaintenanceRequest',
            'description' => 'Track and manage equipment maintenance requests',
            'active' => 1
        ],
        [
            'name' => 'Maintenance Logs',
            'model_type' => 'App\Models\MaintenanceLog',
            'description' => 'Record maintenance history and service records',
            'active' => 1
        ],

        // Storage & Logistics
        [
            'name' => 'Storage Locations',
            'model_type' => 'App\Models\StorageLocation',
            'description' => 'Manage equipment storage locations and capacity',
            'active' => 1
        ],
        [
            'name' => 'Equipment Checkouts',
            'model_type' => 'App\Models\EquipmentCheckout',
            'description' => 'Track equipment loans and usage',
            'active' => 1
        ],
        [
            'name' => 'Equipment Loan ???',
            'model_type' => 'App\Models\EquipmentLoan',
            'description' => 'Manage short-term and long-term equipment loans',
            'active' => 0
        ],

        // Location Management
        [
            'name' => 'System Locations',
            'model_type' => 'App\Models\SystemLocation',
            'description' => 'Manage organization locations and facilities',
            'active' => 1
        ],
        [
            'name' => 'Location Access',
            'model_type' => 'App\Models\SystemLocationAccess',
            'description' => 'Manage location access and security',
            'active' => 1
        ],

        // Workout & Activity Management
        [
            'name' => 'Workouts',
            'model_type' => 'App\Models\Workout',
            'description' => 'Workout Management System',
            'active' => 1
        ],
        [
            'name' => 'Workout Sessions',
            'model_type' => 'App\Models\WorkoutSession',
            'description' => 'Individual workout instances and occurrences',
            'active' => 1
        ],
        [
            'name' => 'Workout Specific Details',
            'model_type' => 'App\Models\WorkoutSpecificDetails',
            'description' => 'Manage workout Specific Details to be Collected',
            'active' => 1
        ],
        [
            'name' => 'Workout Locations',
            'model_type' => 'App\Models\WorkoutLocation',
            'description' => 'Manage workout locations and meeting points',
            'active' => 1
        ],
        [
            'name' => 'Workout Signups',
            'model_type' => 'App\Models\WorkoutSignup',
            'description' => 'User registrations for workouts',
            'active' => 1
        ],
        [
            'name' => 'Meeting Points',
            'model_type' => 'App\Models\WorkoutMeetingPoint',
            'description' => 'Specific meeting points for workouts',
            'active' => 1
        ],
        [
            'name' => 'Workout Feedback',
            'model_type' => 'App\Models\WorkoutFeedback',
            'description' => 'Collect and manage workout feedback',
            'active' => 0
        ],
        [
            'name' => 'Workout Equipment Assignments',
            'model_type' => 'App\Models\WorkoutEquipmentAssignment',
            'description' => 'Workout Equipment Assignments',
        ],
        [
            'name' => 'Tandem Bike Pairing Compatability',
            'model_type' => 'App\Models\TandemBikePairing',
            'description' => 'Guide + Athlete + Bike Compatability Pairings',
        ],
        [
            'name' => 'Weather Records',
            'model_type' => 'App\Models\WorkoutWeather',
            'description' => 'Track weather conditions for workouts',
            'active' => 0
        ],
        [
            'name' => 'Weather Records',
            'model_type' => 'App\Models\WeatherLocation',
            'description' => 'Track weather conditions for Locations',
            'active' => 1
        ],
        [
            'name' => 'Route Difficulty',
            'model_type' => 'App\Models\RouteDifficulty',
            'description' => 'Define and manage route difficulty levels',
            'active' => 0
        ],

        // Event Management
        [
            'name' => 'Events',
            'model_type' => 'App\Models\Event',
            'description' => 'Major events and competitions',
            'active' => 0
        ],
        [
            'name' => 'Event Registrations',
            'model_type' => 'App\Models\EventRegistration',
            'description' => 'Participant registrations for events',
            'active' => 0
        ],
        [
            'name' => 'Event Travel',
            'model_type' => 'App\Models\EventTravel',
            'description' => 'Travel and accommodation arrangements',
            'active' => 0
        ],
        [
            'name' => 'Event Equipment',
            'model_type' => 'App\Models\EventEquipment',
            'description' => 'Equipment assignments for events',
            'active' => 0
        ],
        [
            'name' => 'Event Teams',
            'model_type' => 'App\Models\EventTeam',
            'description' => 'Team management for relay events',
            'active' => 0
        ],
        [
            'name' => 'Event Documents',
            'model_type' => 'App\Models\EventDocument',
            'description' => 'Event-specific documentation and forms',
            'active' => 0
        ],
        [
            'name' => 'Event Results',
            'model_type' => 'App\Models\EventResult',
            'description' => 'Track event results and achievements',
            'active' => 0
        ],
        [
            'name' => 'Event Payments',
            'model_type' => 'App\Models\EventPayment',
            'description' => 'Manage event registration payments',
            'active' => 0
        ],
        [
            'name' => 'Event Accommodations',
            'model_type' => 'App\Models\EventAccommodation',
            'description' => 'Manage lodging and accommodations for events',
            'active' => 0
        ],

        // Volunteer Management
        [
            'name' => 'Volunteer Programs',
            'model_type' => 'App\Models\VolunteerProgram',
            'description' => 'Manage volunteer programs and opportunities',
            'active' => 0
        ],
        [
            'name' => 'Volunteer Hours',
            'model_type' => 'App\Models\VolunteerHours',
            'description' => 'Track volunteer time and contributions',
            'active' => 0
        ],
        [
            'name' => 'Volunteer Recognition',
            'model_type' => 'App\Models\VolunteerRecognition',
            'description' => 'Volunteer awards and recognition system',
            'active' => 0
        ],

        // Safety & Risk Management
        [
            'name' => 'Safety Incidents',
            'model_type' => 'App\Models\SafetyIncident',
            'description' => 'Track and manage safety incidents',
            'active' => 0
        ],
        [
            'name' => 'Safety Incident Severity',
            'model_type' => 'App\Models\SafetyIncidentSeverity',
            'description' => 'Categorize incidents by severity levels',
            'active' => 0
        ],
        [
            'name' => 'Activity Risk',
            'model_type' => 'App\Models\ActivityRisk',
            'description' => 'Track and manage activity risk levels',
            'active' => 0
        ],
        [
            'name' => 'Insurance Coverage',
            'model_type' => 'App\Models\InsuranceCoverage',
            'description' => 'Track and manage insurance coverage',
            'active' => 0
        ],

        // Transportation
        [
            'name' => 'Transportation',
            'model_type' => 'App\Models\Transportation',
            'description' => 'Manage transportation and logistics',
            'active' => 0
        ],
        [
            'name' => 'Vehicles',
            'model_type' => 'App\Models\Vehicle',
            'description' => 'Vehicle management and tracking',
            'active' => 0
        ],
        [
            'name' => 'Vehicle Maintenance',
            'model_type' => 'App\Models\VehicleMaintenance',
            'description' => 'Vehicle maintenance records',
            'active' => 0
        ],

        // Awards & Recognition
        [
            'name' => 'Awards',
            'model_type' => 'App\Models\Award',
            'description' => 'Manage system awards and recognition',
            'active' => 0
        ],
        [
            'name' => 'Achievements',
            'model_type' => 'App\Models\Achievement',
            'description' => 'Track user achievements and milestones',
            'active' => 0
        ],
        [
            'name' => 'Award Ceremonies',
            'model_type' => 'App\Models\AwardCeremony',
            'description' => 'Manage award ceremonies and events',
            'active' => 0
        ],

        // Communication & Reporting
        [
            'name' => 'Communications',
            'model_type' => 'App\Models\Communication',
            'description' => 'System-wide communication management',
            'active' => 0
        ],
        [
            'name' => 'Notifications',
            'model_type' => 'App\Models\Notification',
            'description' => 'User notifications and alerts',
            'active' => 0
        ],
        [
            'name' => 'Reports',
            'model_type' => 'App\Models\Report',
            'description' => 'Custom report generation',
            'active' => 0
        ],
        [
            'name' => 'Analytics',
            'model_type' => 'App\Models\Analytics',
            'description' => 'System analytics and metrics',
            'active' => 0
        ],
        [
            'name' => 'Performance Evaluations',
            'model_type' => 'App\Models\PerformanceEvaluation',
            'description' => 'Monitor and review performance evaluations',
            'active' => 0
        ],

        // Administrative
        [
            'name' => 'Sponsorships',
            'model_type' => 'App\Models\Sponsorship',
            'description' => 'Track and manage event sponsorships',
            'active' => 0
        ],
        [
            'name' => 'Content Moderation',
            'model_type' => 'App\Models\ContentModeration',
            'description' => 'Moderate and manage content submissions',
            'active' => 0
        ],
        [
            'name' => 'Resource Allocation',
            'model_type' => 'App\Models\ResourceAllocation',
            'description' => 'Manage allocation of resources and equipment',
            'active' => 0
        ],
        [
            'name' => 'Qualification Verification',
            'model_type' => 'App\Models\QualificationVerification',
            'description' => 'Verify user qualifications and credentials',
            'active' => 0
        ],
        // Medical & Health Management
        [
            'name' => 'Medical Conditions',
            'model_type' => 'App\Models\MedicalCondition',
            'description' => 'Track participant medical conditions and requirements',
            'active' => 0
        ],
        [
            'name' => 'Medical Alerts',
            'model_type' => 'App\Models\MedicalAlert',
            'description' => 'Manage critical medical notifications and alerts',
            'active' => 0
        ],
        [
            'name' => 'Medications',
            'model_type' => 'App\Models\Medication',
            'description' => 'Track participant medications and schedules',
            'active' => 0
        ],
        // Documentation & Waivers
        [
            'name' => 'Waivers',
            'model_type' => 'App\Models\Waiver',
            'description' => 'Manage liability waivers and consent forms',
            'active' => 0
        ],
        [
            'name' => 'Document Templates',
            'model_type' => 'App\Models\DocumentTemplate',
            'description' => 'Store templates for system-generated documents',
            'active' => 0
        ],
        // Financial Management
        [
            'name' => 'Financial Aid',
            'model_type' => 'App\Models\FinancialAid',
            'description' => 'Manage financial assistance programs',
            'active' => 0
        ],
        [
            'name' => 'Scholarships',
            'model_type' => 'App\Models\Scholarship',
            'description' => 'Track scholarship applications and awards',
            'active' => 0
        ],
        [
            'name' => 'Expense Tracking',
            'model_type' => 'App\Models\ExpenseTracker',
            'description' => 'Monitor program-related expenses',
            'active' => 0
        ],
        // Program Management
        [
            'name' => 'Programs',
            'model_type' => 'App\Models\Program',
            'description' => 'Manage different types of adaptive sports programs',
            'active' => 0
        ],
        [
            'name' => 'Program Sessions',
            'model_type' => 'App\Models\ProgramSession',
            'description' => 'Track individual program sessions and attendance',
            'active' => 0
        ],
        // Support Network
        [
            'name' => 'Assistance Requirements',
            'model_type' => 'App\Models\AssistanceRequirement',
            'description' => 'Manage support and assistance requirements',
            'active' => 0
        ],
        [
            'name' => 'Care Partners',
            'model_type' => 'App\Models\CarePartner',
            'description' => 'Manage care partner relationships and responsibilities',
            'active' => 0
        ],
        [
            'name' => 'Support Teams',
            'model_type' => 'App\Models\SupportTeam',
            'description' => 'Track support teams for participants',
            'active' => 0
        ],
        // Environmental & Facility
        [
            'name' => 'Facilities',
            'model_type' => 'App\Models\Facility',
            'description' => 'Manage training and event facilities',
            'active' => 0
        ],
        [
            'name' => 'Facility Maintenance',
            'model_type' => 'App\Models\FacilityMaintenance',
            'description' => 'Track facility maintenance and inspections',
            'active' => 0
        ],
        [
            'name' => 'Environmental Conditions',
            'model_type' => 'App\Models\EnvironmentalCondition',
            'description' => 'Track environmental factors affecting activities',
            'active' => 0
        ]
    ];

    /**
     * Run the database seeder.
     * Creates or updates system modules while maintaining existing data
     */
    public function run()
    {
        foreach ($this->modules as $moduleData) {
            SystemModule::firstOrCreate(
                ['model_type' => $moduleData['model_type']],
                [
                    'name' => $moduleData['name'],
                    'description' => $moduleData['description'],
                    'active' => $moduleData['active'] ?? true,
                    'metadata' => null
                ]
            );
        }

//        $this->command->info(class_basename(static::class) . ' seeded successfully!');
    }
}
