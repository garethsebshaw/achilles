<?php

namespace Database\Seeders\System;

use App\Models\SystemStatus;
use App\Models\SystemModule;
use Illuminate\Database\Seeder;

class SystemStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getStatusConfigs() as $config) {
            $type = SystemModule::where([
                'model_type' => $config['model_type']
            ])->first();

            if (!$type) {
//                $this->command->warn("Skipping: No system module found for {$config['model_type']}");
                continue; // Skip this module if not found
            }

            foreach ($config['statuses'] as $index => $status) {
                SystemStatus::firstOrCreate(
                    ['code' => $status['code'], 'system_module_id' => $type->id],
                    array_merge($status, [
                        'system_module_id' => $type->id,
                        'is_default' => $index === 0 // Only first status is default
                    ])
                );
            }
        }
//        $this->command->info(class_basename(static::class) . ' seeded successfully!');
    }

    private function getStatusConfigs(): array
    {
        return [
            // Existing modules with expanded statuses
            [
                'model_type' => 'App\Models\Certification',
                'statuses' => [
                    ['name' => 'Active', 'code' => 'cert_active', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Pending', 'code' => 'cert_pending', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Expired', 'code' => 'cert_expired', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Revoked', 'code' => 'cert_revoked', 'color' => '#6c757d', 'is_system' => true],
                    ['name' => 'Suspended', 'code' => 'cert_suspended', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Renewal Required', 'code' => 'cert_renewal', 'color' => '#e83e8c', 'is_system' => true],
                    ['name' => 'Under Review', 'code' => 'cert_review', 'color' => '#20c997', 'is_system' => true]
                ]
            ],
            [
                'model_type' => 'App\Models\WorkoutSignup',
                'statuses' => [
                    ['name' => 'Pending', 'code' => 'signup_pending', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true], // Awaiting confirmation
                    ['name' => 'Confirmed', 'code' => 'signup_confirmed', 'color' => '#28a745', 'is_system' => true], // Officially signed up
                    ['name' => 'Cancelled', 'code' => 'signup_cancelled', 'color' => '#dc3545', 'is_system' => true], // Withdrawn signup
                    ['name' => 'Attended', 'code' => 'signup_attended', 'color' => '#007bff', 'is_system' => true], // Successfully attended
                    ['name' => 'No Show', 'code' => 'signup_no_show', 'color' => '#6c757d', 'is_system' => true], // Didn't attend
                    ['name' => 'Checked In', 'code' => 'signup_checked_in', 'color' => '#17a2b8', 'is_system' => true], // Arrived at the event
                    ['name' => 'Checked Out', 'code' => 'signup_checked_out', 'color' => '#fd7e14', 'is_system' => true], // Left the event
                    ['name' => 'Waitlisted', 'code' => 'signup_waitlisted', 'color' => '#e83e8c', 'is_system' => true], // On the waitlist
                    ['name' => 'Late Cancellation', 'code' => 'signup_late_cancel', 'color' => '#ff851b', 'is_system' => true], // Cancelled close to event time
                    ['name' => 'Banned', 'code' => 'signup_banned', 'color' => '#343a40', 'is_system' => true], // Prevented from attending
                ]
            ],
            [
                'model_type' => 'App\Models\Equipment',
                'statuses' => [
                    ['name' => 'Available', 'code' => 'equip_available', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'In Use', 'code' => 'equip_in_use', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Under Maintenance', 'code' => 'equip_maintenance', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Out of Service', 'code' => 'equip_out_of_service', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Reserved', 'code' => 'equip_reserved', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Pending Inspection', 'code' => 'equip_inspection', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Retired', 'code' => 'equip_retired', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                'model_type' => 'App\Models\TandemBikePairing',
                'statuses' => [
                    ['name' => 'Green - Fully Compatible', 'code' => 'compat_green', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Amber - Partially Compatible', 'code' => 'compat_amber', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Red - Not Compatible', 'code' => 'compat_red', 'color' => '#dc3545', 'is_system' => true],
                ]
            ],

            // New modules with statuses
            [
                'model_type' => 'App\Models\Workout',
                'statuses' => [
                    ['name' => 'Active', 'code' => 'workout_active', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'workout_cancelled', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Postponed', 'code' => 'workout_postponed', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'workout_completed', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Draft', 'code' => 'workout_draft', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                'model_type' => 'App\Models\WorkoutSession',
                'statuses' => [
                    ['name' => 'Scheduled', 'code' => 'session_scheduled', 'color' => '#17a2b8', 'is_default' => true, 'is_system' => true],
                    ['name' => 'In Progress', 'code' => 'session_progress', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'session_completed', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'session_cancelled', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Weather Hold', 'code' => 'session_weather', 'color' => '#ffc107', 'is_system' => true]
                ]
            ],
            [
                'model_type' => 'App\Models\GuideTraining',
                'statuses' => [
                    ['name' => 'Not Started', 'code' => 'guide_not_started', 'color' => '#6c757d', 'is_default' => true, 'is_system' => true],
                    ['name' => 'In Progress', 'code' => 'guide_progress', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'guide_completed', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Needs Review', 'code' => 'guide_review', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Requires Renewal', 'code' => 'guide_renewal', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Expired', 'code' => 'guide_expired', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                'model_type' => 'App\Models\EquipmentMaintenance',
                'statuses' => [
                    ['name' => 'Scheduled', 'code' => 'maint_scheduled', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'In Progress', 'code' => 'maint_progress', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'maint_completed', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Delayed', 'code' => 'maint_delayed', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Requires Parts', 'code' => 'maint_parts', 'color' => '#e83e8c', 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'maint_cancelled', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Event Statuses
                'model_type' => 'App\Models\Event',
                'statuses' => [
                    ['name' => 'Draft', 'code' => 'event_draft', 'color' => '#6c757d', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Published', 'code' => 'event_published', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Registration Open', 'code' => 'event_reg_open', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Registration Closed', 'code' => 'event_reg_closed', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'In Progress', 'code' => 'event_progress', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'event_completed', 'color' => '#0d6efd', 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'event_cancelled', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Event Registration Statuses
                'model_type' => 'App\Models\EventRegistration',
                'statuses' => [
                    ['name' => 'Pending', 'code' => 'reg_pending', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Confirmed', 'code' => 'reg_confirmed', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Waitlisted', 'code' => 'reg_waitlist', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'reg_cancelled', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Deferred', 'code' => 'reg_deferred', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Volunteer Program Statuses
                'model_type' => 'App\Models\VolunteerProgram',
                'statuses' => [
                    ['name' => 'Active', 'code' => 'vol_active', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Planning', 'code' => 'vol_planning', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'On Hold', 'code' => 'vol_hold', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'vol_completed', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'vol_cancelled', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Safety Incident Statuses
                'model_type' => 'App\Models\SafetyIncident',
                'statuses' => [
                    ['name' => 'Reported', 'code' => 'incident_reported', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Under Investigation', 'code' => 'incident_investigating', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Action Required', 'code' => 'incident_action', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Resolved', 'code' => 'incident_resolved', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Closed', 'code' => 'incident_closed', 'color' => '#6c757d', 'is_system' => true],
                    ['name' => 'Escalated', 'code' => 'incident_escalated', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Vehicle Statuses
                'model_type' => 'App\Models\Vehicle',
                'statuses' => [
                    ['name' => 'Available', 'code' => 'vehicle_available', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'In Use', 'code' => 'vehicle_in_use', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Maintenance', 'code' => 'vehicle_maintenance', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Out of Service', 'code' => 'vehicle_out', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Reserved', 'code' => 'vehicle_reserved', 'color' => '#fd7e14', 'is_system' => true]
                ]
            ],
            [
                // Transportation Request Statuses
                'model_type' => 'App\Models\Transportation',
                'statuses' => [
                    ['name' => 'Pending', 'code' => 'transport_pending', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Approved', 'code' => 'transport_approved', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'In Progress', 'code' => 'transport_progress', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'transport_completed', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'transport_cancelled', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Award/Achievement Statuses
                'model_type' => 'App\Models\Achievement',
                'statuses' => [
                    ['name' => 'In Progress', 'code' => 'achieve_progress', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'achieve_completed', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Awarded', 'code' => 'achieve_awarded', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Expired', 'code' => 'achieve_expired', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Communication Statuses
                'model_type' => 'App\Models\Communication',
                'statuses' => [
                    ['name' => 'Draft', 'code' => 'comm_draft', 'color' => '#6c757d', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Scheduled', 'code' => 'comm_scheduled', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Sent', 'code' => 'comm_sent', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Failed', 'code' => 'comm_failed', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'comm_cancelled', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Incident Severity Levels
                'model_type' => 'App\Models\SafetyIncidentSeverity',
                'statuses' => [
                    ['name' => 'Critical', 'code' => 'severity_critical', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'High', 'code' => 'severity_high', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Medium', 'code' => 'severity_medium', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Low', 'code' => 'severity_low', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Negligible', 'code' => 'severity_negligible', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Report Statuses
                'model_type' => 'App\Models\Report',
                'statuses' => [
                    ['name' => 'Draft', 'code' => 'report_draft', 'color' => '#6c757d', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Pending Review', 'code' => 'report_pending', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Published', 'code' => 'report_published', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Archived', 'code' => 'report_archived', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Needs Update', 'code' => 'report_update', 'color' => '#fd7e14', 'is_system' => true]
                ]
            ],
            [
                // Award Ceremony Statuses
                'model_type' => 'App\Models\AwardCeremony',
                'statuses' => [
                    ['name' => 'Planning', 'code' => 'ceremony_planning', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Scheduled', 'code' => 'ceremony_scheduled', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'In Progress', 'code' => 'ceremony_progress', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'ceremony_completed', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'ceremony_cancelled', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Vehicle Maintenance Types
                'model_type' => 'App\Models\VehicleMaintenance',
                'statuses' => [
                    ['name' => 'Routine', 'code' => 'vmaint_routine', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Preventive', 'code' => 'vmaint_preventive', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Repair', 'code' => 'vmaint_repair', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Emergency', 'code' => 'vmaint_emergency', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Inspection', 'code' => 'vmaint_inspection', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Emergency Contact Status
                'model_type' => 'App\Models\EmergencyContact',
                'statuses' => [
                    ['name' => 'Active', 'code' => 'contact_active', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Needs Update', 'code' => 'contact_update', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Inactive', 'code' => 'contact_inactive', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Verified', 'code' => 'contact_verified', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Unverified', 'code' => 'contact_unverified', 'color' => '#fd7e14', 'is_system' => true]
                ]
            ],
            [
                // Volunteer Hours Status
                'model_type' => 'App\Models\VolunteerHours',
                'statuses' => [
                    ['name' => 'Pending', 'code' => 'hours_pending', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Approved', 'code' => 'hours_approved', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Rejected', 'code' => 'hours_rejected', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Needs Review', 'code' => 'hours_review', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Adjusted', 'code' => 'hours_adjusted', 'color' => '#fd7e14', 'is_system' => true]
                ]
            ],
            [
                // Analytics Status Types
                'model_type' => 'App\Models\Analytics',
                'statuses' => [
                    ['name' => 'Processing', 'code' => 'analytics_processing', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Ready', 'code' => 'analytics_ready', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Failed', 'code' => 'analytics_failed', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Outdated', 'code' => 'analytics_outdated', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Scheduled', 'code' => 'analytics_scheduled', 'color' => '#17a2b8', 'is_system' => true]
                ]
            ],
            [
                // Document Statuses
                'model_type' => 'App\Models\EventDocument',
                'statuses' => [
                    ['name' => 'Draft', 'code' => 'doc_draft', 'color' => '#6c757d', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Under Review', 'code' => 'doc_review', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Approved', 'code' => 'doc_approved', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Needs Update', 'code' => 'doc_update', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Archived', 'code' => 'doc_archived', 'color' => '#6c757d', 'is_system' => true],
                    ['name' => 'Expired', 'code' => 'doc_expired', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Recognition/Award Levels
                'model_type' => 'App\Models\VolunteerRecognition',
                'statuses' => [
                    ['name' => 'Bronze', 'code' => 'award_bronze', 'color' => '#CD7F32', 'is_system' => true],
                    ['name' => 'Silver', 'code' => 'award_silver', 'color' => '#C0C0C0', 'is_system' => true],
                    ['name' => 'Gold', 'code' => 'award_gold', 'color' => '#FFD700', 'is_system' => true],
                    ['name' => 'Platinum', 'code' => 'award_platinum', 'color' => '#E5E4E2', 'is_system' => true],
                    ['name' => 'Diamond', 'code' => 'award_diamond', 'color' => '#B9F2FF', 'is_system' => true],
                    ['name' => 'Lifetime', 'code' => 'award_lifetime', 'color' => '#4B0082', 'is_system' => true]
                ]
            ],
            [
                // Equipment Request Statuses
                'model_type' => 'App\Models\EventEquipment',
                'statuses' => [
                    ['name' => 'Requested', 'code' => 'equip_requested', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Approved', 'code' => 'equip_approved', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Assigned', 'code' => 'equip_assigned', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'In Use', 'code' => 'equip_in_use', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Returned', 'code' => 'equip_returned', 'color' => '#6c757d', 'is_system' => true],
                    ['name' => 'Denied', 'code' => 'equip_denied', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'On Hold', 'code' => 'equip_hold', 'color' => '#fd7e14', 'is_system' => true]
                ]
            ],
            [
                // Event Travel Status
                'model_type' => 'App\Models\EventTravel',
                'statuses' => [
                    ['name' => 'Planning', 'code' => 'travel_planning', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Booked', 'code' => 'travel_booked', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'In Transit', 'code' => 'travel_transit', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Arrived', 'code' => 'travel_arrived', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Delayed', 'code' => 'travel_delayed', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'travel_cancelled', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'travel_completed', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Team Status Types (for relays/events)
                'model_type' => 'App\Models\EventTeam',
                'statuses' => [
                    ['name' => 'Forming', 'code' => 'team_forming', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Complete', 'code' => 'team_complete', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Needs Members', 'code' => 'team_needs_members', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Ready', 'code' => 'team_ready', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Active', 'code' => 'team_active', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Finished', 'code' => 'team_finished', 'color' => '#6c757d', 'is_system' => true],
                    ['name' => 'Withdrawn', 'code' => 'team_withdrawn', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Result Verification Statuses
                'model_type' => 'App\Models\EventResult',
                'statuses' => [
                    ['name' => 'Pending', 'code' => 'result_pending', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Verified', 'code' => 'result_verified', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Under Review', 'code' => 'result_review', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Disputed', 'code' => 'result_disputed', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Corrected', 'code' => 'result_corrected', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Official', 'code' => 'result_official', 'color' => '#0d6efd', 'is_system' => true],
                    ['name' => 'DNF', 'code' => 'result_dnf', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Weather Condition Statuses
                'model_type' => 'App\Models\WorkoutWeather',
                'statuses' => [
                    ['name' => 'Clear', 'code' => 'weather_clear', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Light Rain', 'code' => 'weather_light_rain', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Heavy Rain', 'code' => 'weather_heavy_rain', 'color' => '#0d6efd', 'is_system' => true],
                    ['name' => 'Snow', 'code' => 'weather_snow', 'color' => '#6c757d', 'is_system' => true],
                    ['name' => 'Extreme Heat', 'code' => 'weather_heat', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Storm Warning', 'code' => 'weather_storm', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'High Wind', 'code' => 'weather_wind', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Poor Air Quality', 'code' => 'weather_air', 'color' => '#6610f2', 'is_system' => true]
                ]
            ],
            [
                // Equipment Maintenance Priority
                'model_type' => 'App\Models\EquipmentMaintenancePriority',
                'statuses' => [
                    ['name' => 'Critical', 'code' => 'maint_critical', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'High', 'code' => 'maint_high', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Normal', 'code' => 'maint_normal', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Low', 'code' => 'maint_low', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Optional', 'code' => 'maint_optional', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Training Progress Levels
                'model_type' => 'App\Models\GuideTraining',
                'statuses' => [
                    ['name' => 'Not Started', 'code' => 'training_not_started', 'color' => '#6c757d', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Theory In Progress', 'code' => 'training_theory', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Practical In Progress', 'code' => 'training_practical', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Assessment Pending', 'code' => 'training_assessment', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Completed', 'code' => 'training_completed', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Needs Review', 'code' => 'training_review', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Certified', 'code' => 'training_certified', 'color' => '#20c997', 'is_system' => true]
                ]
            ],
            [
                // Notification Priorities
                'model_type' => 'App\Models\Notification',
                'statuses' => [
                    ['name' => 'Urgent', 'code' => 'notif_urgent', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'High', 'code' => 'notif_high', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Normal', 'code' => 'notif_normal', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Low', 'code' => 'notif_low', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Info', 'code' => 'notif_info', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // User Feedback Statuses
                'model_type' => 'App\Models\WorkoutFeedback',
                'statuses' => [
                    ['name' => 'Submitted', 'code' => 'feedback_submitted', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Under Review', 'code' => 'feedback_review', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Addressed', 'code' => 'feedback_addressed', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Needs Follow-up', 'code' => 'feedback_followup', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Resolved', 'code' => 'feedback_resolved', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Escalated', 'code' => 'feedback_escalated', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Equipment Loan Periods
                'model_type' => 'App\Models\EquipmentLoan',
                'statuses' => [
                    ['name' => 'Short Term', 'code' => 'loan_short', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Long Term', 'code' => 'loan_long', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Event Only', 'code' => 'loan_event', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Permanent', 'code' => 'loan_permanent', 'color' => '#6c757d', 'is_system' => true],
                    ['name' => 'Overdue', 'code' => 'loan_overdue', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Extended', 'code' => 'loan_extended', 'color' => '#fd7e14', 'is_system' => true]
                ]
            ],
            [
                // Performance Evaluation Levels
                'model_type' => 'App\Models\PerformanceEvaluation',
                'statuses' => [
                    ['name' => 'Exceptional', 'code' => 'perf_exceptional', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Above Average', 'code' => 'perf_above', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Meets Expectations', 'code' => 'perf_meets', 'color' => '#17a2b8', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Needs Improvement', 'code' => 'perf_improve', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Unsatisfactory', 'code' => 'perf_unsat', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Under Review', 'code' => 'perf_review', 'color' => '#fd7e14', 'is_system' => true]
                ]
            ],
            [
                // Event Registration Payment Statuses
                'model_type' => 'App\Models\EventPayment',
                'statuses' => [
                    ['name' => 'Pending', 'code' => 'payment_pending', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Paid', 'code' => 'payment_paid', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Partially Paid', 'code' => 'payment_partial', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Refunded', 'code' => 'payment_refunded', 'color' => '#6c757d', 'is_system' => true],
                    ['name' => 'Failed', 'code' => 'payment_failed', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Waived', 'code' => 'payment_waived', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Disputed', 'code' => 'payment_disputed', 'color' => '#fd7e14', 'is_system' => true]
                ]
            ],
            [
                // Accommodation Booking Statuses
                'model_type' => 'App\Models\EventAccommodation',
                'statuses' => [
                    ['name' => 'Pending', 'code' => 'accom_pending', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Confirmed', 'code' => 'accom_confirmed', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Checked In', 'code' => 'accom_checked_in', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Checked Out', 'code' => 'accom_checked_out', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Cancelled', 'code' => 'accom_cancelled', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Modified', 'code' => 'accom_modified', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'No Show', 'code' => 'accom_no_show', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Content Moderation Statuses
                'model_type' => 'App\Models\ContentModeration',
                'statuses' => [
                    ['name' => 'Pending Review', 'code' => 'mod_pending', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Approved', 'code' => 'mod_approved', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Rejected', 'code' => 'mod_rejected', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Needs Edits', 'code' => 'mod_needs_edits', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Flagged', 'code' => 'mod_flagged', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Under Appeal', 'code' => 'mod_appeal', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Resource Allocation Statuses
                'model_type' => 'App\Models\ResourceAllocation',
                'statuses' => [
                    ['name' => 'Available', 'code' => 'resource_available', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Allocated', 'code' => 'resource_allocated', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Reserved', 'code' => 'resource_reserved', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Depleted', 'code' => 'resource_depleted', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'On Hold', 'code' => 'resource_hold', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Maintenance', 'code' => 'resource_maintenance', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Sponsorship Levels
                'model_type' => 'App\Models\Sponsorship',
                'statuses' => [
                    ['name' => 'Platinum', 'code' => 'sponsor_platinum', 'color' => '#E5E4E2', 'is_system' => true],
                    ['name' => 'Gold', 'code' => 'sponsor_gold', 'color' => '#FFD700', 'is_system' => true],
                    ['name' => 'Silver', 'code' => 'sponsor_silver', 'color' => '#C0C0C0', 'is_system' => true],
                    ['name' => 'Bronze', 'code' => 'sponsor_bronze', 'color' => '#CD7F32', 'is_system' => true],
                    ['name' => 'Supporting', 'code' => 'sponsor_supporting', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'In-Kind', 'code' => 'sponsor_inkind', 'color' => '#17a2b8', 'is_system' => true]
                ]
            ],
            [
                // Qualification Verification Statuses
                'model_type' => 'App\Models\QualificationVerification',
                'statuses' => [
                    ['name' => 'Submitted', 'code' => 'qual_submitted', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Under Review', 'code' => 'qual_review', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Verified', 'code' => 'qual_verified', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Rejected', 'code' => 'qual_rejected', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Additional Info Needed', 'code' => 'qual_info_needed', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Expired', 'code' => 'qual_expired', 'color' => '#6c757d', 'is_system' => true],
                    ['name' => 'Revoked', 'code' => 'qual_revoked', 'color' => '#6610f2', 'is_system' => true]
                ]
            ],
            [
                // Accessibility Requirements
                'model_type' => 'App\Models\AccessibilityRequirement',
                'statuses' => [
                    ['name' => 'Required', 'code' => 'access_required', 'color' => '#dc3545', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Preferred', 'code' => 'access_preferred', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Optional', 'code' => 'access_optional', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Not Needed', 'code' => 'access_not_needed', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Under Assessment', 'code' => 'access_assessment', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Temporary', 'code' => 'access_temporary', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Insurance Coverage Status
                'model_type' => 'App\Models\InsuranceCoverage',
                'statuses' => [
                    ['name' => 'Active', 'code' => 'insurance_active', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Pending', 'code' => 'insurance_pending', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Expired', 'code' => 'insurance_expired', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Needs Review', 'code' => 'insurance_review', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Insufficient', 'code' => 'insurance_insufficient', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Waiver Required', 'code' => 'insurance_waiver', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Dietary Preferences
                'model_type' => 'App\Models\DietaryPreference',
                'statuses' => [
                    ['name' => 'Required', 'code' => 'diet_required', 'color' => '#dc3545', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Preferred', 'code' => 'diet_preferred', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Medical', 'code' => 'diet_medical', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Religious', 'code' => 'diet_religious', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Cultural', 'code' => 'diet_cultural', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'No Restrictions', 'code' => 'diet_none', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],

            [
                // Language Proficiency
                'model_type' => 'App\Models\LanguageProficiency',
                'statuses' =>
                    [
                        [
                            'name' => '1st / Native',
                            'description' => 'Complete mastery of the language, like a native speaker.',
                            'code' => 'lang_native',
                            'color' => '#28a745',
                            'is_default' => true,
                            'is_system' => true
                        ],
                        [
                            'name' => 'Fluent',
                            'description' => 'Can communicate easily and naturally in all situations.',
                            'code' => 'lang_fluent',
                            'color' => '#20c997',
                            'is_system' => true
                        ],
                        [
                            'name' => 'Advanced',
                            'description' => 'Can handle complex conversations and topics but may make minor mistakes.',
                            'code' => 'lang_advanced',
                            'color' => '#17a2b8',
                            'is_system' => true
                        ],
                        [
                            'name' => 'Intermediate',
                            'description' => 'Can handle everyday conversations but lacks precision.',
                            'code' => 'lang_intermediate',
                            'color' => '#ffc107',
                            'is_system' => true
                        ],
                        [
                            'name' => 'Basic',
                            'description' => 'Can use simple phrases and understand basic words, but communication is limited.',
                            'code' => 'lang_basic',
                            'color' => '#e83e8c',
                            'is_system' => true
                        ],
                        [
                            'name' => 'Limited',
                            'description' => 'Recognizes a few words but cannot construct sentences.',
                            'code' => 'lang_limited',
                            'color' => '#6c757d',
                            'is_system' => true
                        ],
                        [
                            'name' => 'Learning',
                            'description' => 'No current ability to understand or use the language.',
                            'code' => 'lang_none',
                            'color' => '#dc3545',
                            'is_system' => true
                        ]
                    ]

        ],
            [
                // Equipment Customization Stages
                'model_type' => 'App\Models\EquipmentCustomization',
                'statuses' => [
                    ['name' => 'Assessment Needed', 'code' => 'custom_assessment', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Design Phase', 'code' => 'custom_design', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'In Progress', 'code' => 'custom_progress', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Testing', 'code' => 'custom_testing', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Complete', 'code' => 'custom_complete', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Needs Adjustment', 'code' => 'custom_adjustment', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Not Required', 'code' => 'custom_not_required', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Equipment Fitting Stages
                'model_type' => 'App\Models\EquipmentFitting',
                'statuses' => [
                    ['name' => 'Initial Assessment', 'code' => 'fitting_assess', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Measurements Taken', 'code' => 'fitting_measured', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Adjustments Needed', 'code' => 'fitting_adjustments', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Ready for Testing', 'code' => 'fitting_testing', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Final Fitting', 'code' => 'fitting_final', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Refit Required', 'code' => 'fitting_refit', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Fitted', 'code' => 'fitting_complete', 'color' => '#0d6efd', 'is_system' => true]
                ]
            ],
            [
                // Assistance Requirements
                'model_type' => 'App\Models\AssistanceRequirement',
                'statuses' => [
                    ['name' => 'None Required', 'code' => 'assist_none', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Light Support', 'code' => 'assist_light', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Moderate Support', 'code' => 'assist_moderate', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Full Support', 'code' => 'assist_full', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Multiple Guides', 'code' => 'assist_multi_guide', 'color' => '#dc3545', 'is_system' => true],
                    ['name' => 'Specialized Equipment', 'code' => 'assist_equipment', 'color' => '#6c757d', 'is_system' => true]
                ]
            ],
            [
                // Activity Risk Levels
                'model_type' => 'App\Models\ActivityRisk',
                'statuses' => [
                    ['name' => 'Low Risk', 'code' => 'risk_low', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Moderate Risk', 'code' => 'risk_moderate', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'High Risk', 'code' => 'risk_high', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Weather Dependent', 'code' => 'risk_weather', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Experience Required', 'code' => 'risk_experience', 'color' => '#6c757d', 'is_system' => true],
                    ['name' => 'Not Recommended', 'code' => 'risk_not_recommended', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Guide Experience Levels
                'model_type' => 'App\Models\GuideExperience',
                'statuses' => [
                    ['name' => 'Novice', 'code' => 'guide_novice', 'color' => '#ffc107', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Intermediate', 'code' => 'guide_intermediate', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Experienced', 'code' => 'guide_experienced', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Expert', 'code' => 'guide_expert', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Lead Guide', 'code' => 'guide_lead', 'color' => '#0d6efd', 'is_system' => true],
                    ['name' => 'Guide Trainer', 'code' => 'guide_trainer', 'color' => '#6610f2', 'is_system' => true]
                ]
            ],
            [
                // Equipment Condition Ratings
                'model_type' => 'App\Models\EquipmentCondition',
                'statuses' => [
                    ['name' => 'New', 'code' => 'condition_new', 'color' => '#28a745', 'is_system' => true],
                    ['name' => 'Excellent', 'code' => 'condition_excellent', 'color' => '#20c997', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Good', 'code' => 'condition_good', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Fair', 'code' => 'condition_fair', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Poor', 'code' => 'condition_poor', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'End of Life', 'code' => 'condition_eol', 'color' => '#dc3545', 'is_system' => true]
                ]
            ],
            [
                // Route Difficulty Levels
                'model_type' => 'App\Models\RouteDifficulty',
                'statuses' => [
                    ['name' => 'Beginner', 'code' => 'route_beginner', 'color' => '#28a745', 'is_default' => true, 'is_system' => true],
                    ['name' => 'Easy', 'code' => 'route_easy', 'color' => '#20c997', 'is_system' => true],
                    ['name' => 'Moderate', 'code' => 'route_moderate', 'color' => '#17a2b8', 'is_system' => true],
                    ['name' => 'Challenging', 'code' => 'route_challenging', 'color' => '#ffc107', 'is_system' => true],
                    ['name' => 'Advanced', 'code' => 'route_advanced', 'color' => '#fd7e14', 'is_system' => true],
                    ['name' => 'Technical', 'code' => 'route_technical', 'color' => '#dc3545', 'is_system' => true]
                ]
            ]
        ];
    }
}
