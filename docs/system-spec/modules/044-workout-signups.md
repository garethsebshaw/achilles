# Workout Signups

- Module ID: `44`
- Business Domain: `Workouts`
- Current Status: `Implemented`
- Model Reference: `App\Models\WorkoutSignup`

## What This Module Does

This module is live in the codebase today. In simple terms, it covers user registrations for workouts.

## Who Uses It

- Athletes, guides, check-in staff, and chapter admins all depend on this module family.
- System admins use it for policy, data correction, and operational reporting.

## Current State

- A model file exists at `WorkoutSignup.php`.
- A Nova admin resource exists at `WorkoutSignup.php`.

## Working Surfaces

- Data model: `app/Models/WorkoutSignup.php`
- Primary Nova resource: `app/Nova/WorkoutSignup.php`
- Controller/API reference: `app/Http/Controllers/Api/WorkoutSessionAttendanceController.php`
- Controller/API reference: `app/Http/Controllers/WorkoutSignupController.php`
- Route wiring: `routes/web.php`
- Nova supporting surface: `app/Nova/Actions/CheckInAction.php`
- Nova supporting surface: `app/Nova/Actions/CheckOutAction.php`
- Nova supporting surface: `app/Nova/Actions/ManageWorkoutAttendance.php`
- Nova supporting surface: `app/Nova/Actions/RefreshSessionWeather.php`
- Nova supporting surface: `app/Nova/Actions/EndCheckInStaffSession.php`
- Nova supporting surface: `app/Nova/Filters/WorkoutSignupDateRangeFilter.php`
- Nova supporting surface: `app/Nova/Filters/WorkoutSignupUserFilter.php`
- Nova supporting surface: `app/Nova/Lenses/SessionTimeLens.php`
- Nova supporting surface: `app/Nova/Lenses/SessionWithin12Hours.php`
- Nova supporting surface: `app/Nova/Lenses/SessionWithin3Hours.php`

## Information This Module Expects

- `workout_session_id` - Workout session id.
- `user_id` - User id.
- `athlete_id` - Optional athlete assignment for guide signups.
- `preferences` - Preferences.
- `equipment_requirements` - Equipment requirements.
- `status_id` - Status id.
- `checked_in_at` - Check-in timestamp.
- `checked_out_at` - Check-out timestamp.

## Main Connections

- `workoutSession` relationship via `belongsTo`.
- `user` relationship via `belongsTo`.
- `athleteUser` relationship via `belongsTo`.
- `status` relationship via `belongsTo`.
- `specificDetails` relationship via `hasOne`.
- `equipmentAssignments` relationship via `hasMany`.

## Core Workflows

- Create or confirm participation records.
- Restrict operational workflows to scoped session context instead of global browsing.
- Track status transitions and attendance outcomes cleanly.
- Allow check-in staff to add walk-in athletes or guides into the currently active workout session.
- Reassign guide signups to a different athlete within the same session from the signup edit surface.
- Show athlete-guide relationship hints inline so staff can move quickly through related arrivals.

## Admin, Metrics, Filters, And Lenses

- Filters should default to chapter and date-window scope rather than global result sets.
- Metrics should highlight today, this week, next week, and exception states.
- Lenses should separate operational action queues from historical reporting views.

## Access And Scope Rules

- Global admins may need full visibility, but day-to-day users should default to chapter-scoped data.
- If the module touches athletes, guides, or operational rosters, avoid global unfiltered lists by default.
- Session-scoped and chapter-scoped access should take priority over generic Nova access to avoid attendance mistakes and heavy queries.

## Localization And Accessibility Notes

- All user-facing labels, statuses, button text, and helper text must go through the locale system with English kept complete.
- If the module exposes participant-facing surfaces, plan for screen-reader compatibility and clear language switching from the start.

## What Is Still Missing Or Risky

- Keep the fields, relationships, and admin surface aligned as the code evolves.
- Re-test this module whenever related chapter scope, locale, performance, or authorization work changes.

## Recommended Build Or Maintenance Notes

- Any material workflow change in this module should update this spec file before the task is considered complete.
- If the visible surface changes, refresh locale strings and rerun the module reference export.
