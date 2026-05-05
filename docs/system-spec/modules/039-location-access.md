# Location Access

- Module ID: `39`
- Business Domain: `Geography`
- Current Status: `Implemented`
- Model Reference: `App\Models\SystemLocationAccess`

## What This Module Does

This module is live in the codebase today. In simple terms, it covers manage location access and security.

## Who Uses It

- System admins and chapter admins maintain this data.
- Most other users consume it indirectly through workouts, events, equipment, and weather.

## Current State

- A model file exists at `SystemLocationAccess.php`.
- A Nova admin resource exists at `SystemLocationAccess.php`.

## Working Surfaces

- Data model: `app/Models/SystemLocationAccess.php`
- Primary Nova resource: `app/Nova/SystemLocationAccess.php`
- Nova supporting surface: `app/Nova/Filters/UserLocationAccessFilter.php`
- Nova supporting surface: `app/Nova/Lenses/UserLens.php`
- Nova supporting surface: `app/Nova/Metrics/UsersWithActiveLocationAccessMetric.php`
- Nova supporting surface: `app/Nova/SystemLocation.php`
- Nova supporting surface: `app/Nova/User.php`

## Information This Module Expects

- `location_id` - Location id.
- `user_id` - User id.
- `access_type` - Access type.
- `access_identifier` - Access identifier.
- `access_granted_date` - Access granted date.
- `access_expiry_date` - Access expiry date.
- `granted_by_id` - Granted by id.
- `is_active` - Is active.
- `notes` - Notes.

## Main Connections

- `location` relationship via `belongsTo`.
- `user` relationship via `belongsTo`.
- `grantedBy` relationship via `belongsTo`.

## Core Workflows

- Create and maintain records through scoped admin workflows.
- Search, filter, and review records without loading the entire dataset at once.
- Use this module as part of larger chapter, user, workout, or event workflows.
- Derive chapter-level operational scope for session check-in workflows when staff are not global admins.

## Admin, Metrics, Filters, And Lenses

- Filters should reduce the dataset to role-, chapter-, and time-relevant slices.
- Metrics should answer the first operational question without requiring staff to run reports.
- Lenses should separate action-needed records from clean background history.

## Access And Scope Rules

- Global admins may need full visibility, but day-to-day users should default to chapter-scoped data.
- If the module touches athletes, guides, or operational rosters, avoid global unfiltered lists by default.

## Localization And Accessibility Notes

- All user-facing labels, statuses, button text, and helper text must go through the locale system with English kept complete.
- If the module exposes participant-facing surfaces, plan for screen-reader compatibility and clear language switching from the start.

## What Is Still Missing Or Risky

- Keep the fields, relationships, and admin surface aligned as the code evolves.
- Re-test this module whenever related chapter scope, locale, performance, or authorization work changes.

## Recommended Build Or Maintenance Notes

- Any material workflow change in this module should update this spec file before the task is considered complete.
- If the visible surface changes, refresh locale strings and rerun the module reference export.
