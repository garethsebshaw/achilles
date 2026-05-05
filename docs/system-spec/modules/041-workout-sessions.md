# Workout Sessions

- Module ID: `41`
- Business Domain: `Workouts`
- Current Status: `Implemented`
- Model Reference: `App\Models\WorkoutSession`

## What This Module Does

This module is live in the codebase today. In simple terms, it covers individual workout instances and occurrences.

## Who Uses It

- Athletes, guides, check-in staff, and chapter admins all depend on this module family.
- System admins use it for policy, data correction, and operational reporting.

## Current State

- A model file exists at `WorkoutSession.php`.
- A Nova admin resource exists at `WorkoutSession.php`.

## Working Surfaces

- Data model: `app/Models/WorkoutSession.php`
- Primary Nova resource: `app/Nova/WorkoutSession.php`
- Controller/API reference: `app/Http/Controllers/Api/WorkoutSessionAttendanceController.php`
- Controller/API reference: `app/Http/Controllers/WorkoutSessionController.php`
- Controller/API reference: `app/Http/Controllers/WorkoutSignupController.php`
- Route wiring: `routes/api.php`
- Route wiring: `routes/web.php`
- Nova supporting surface: `app/Nova/Actions/ViewSessionUsers.php`
- Nova supporting surface: `app/Nova/Actions/GenerateDemoSessionSignups.php`
- Nova supporting surface: `app/Nova/Filters/WorkoutSessionFilter.php`
- Nova supporting surface: `app/Nova/Filters/WorkoutSignupDateRangeFilter.php`
- Nova supporting surface: `app/Nova/Filters/WorkoutSignupUserFilter.php`
- Nova supporting surface: `app/Nova/Lenses/SessionTimeLens.php`
- Nova supporting surface: `app/Nova/Lenses/SessionWithin12Hours.php`
- Nova supporting surface: `app/Nova/Lenses/SessionWithin3Hours.php`
- Nova supporting surface: `app/Nova/Lenses/SessionWithin6Hours.php`

## Information This Module Expects

- `workout_id` - Workout id.
- `workout_version` - Workout version.
- `location_id` - Location id.
- `session_date` - Session date.
- `start_time` - Start time.
- `end_time` - End time.
- `max_athletes` - Max athletes.
- `max_guides` - Max guides.
- `status_id` - Status id.
- `weather_conditions` - Weather conditions.
- `cancellation_reason` - Cancellation reason.
- `cancelled_by` - Cancelled by.
- `cancelled_at` - Cancelled at.
- `notes` - Notes.
- `metadata` - Metadata.

## Main Connections

- `workout` relationship via `belongsTo`.
- `location` relationship via `belongsTo`.
- `status` relationship via `belongsTo`.
- `cancelledBy` relationship via `belongsTo`.
- `signups` relationship via `hasMany`.
- `meetingPoints` relationship via `belongsTo`.

## Core Workflows

- Create and manage the time-bound records.
- Filter quickly by date window, chapter scope, and operational status.
- Start and stop a chapter-scoped check-in staff session from the session page.
- Use the session context to drive related signups, weather refresh, walk-in check-in, or downstream actions.
- Generate dense demo signup rosters for selected sessions when load-testing or QA requires heavier attendance data.

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
