# Codex Start Here

## What This Repository Actually Is

This is a Laravel + Nova operations system for Achilles workflows. Despite the repository name and the older workout-centric documentation, the codebase spans multiple domains:

- workouts and attendance
- equipment and maintenance
- users, chapters, and locations
- certifications and languages
- weather data
- system metadata and audit structures

The best source of truth is the code, especially:

- `database/migrations/`
- `database/seeders/`
- `app/Models/`
- `app/Nova/`
- `routes/`

## Broad Module Comparison

The external "Achilles Module Overview with Market Rate Pricing" document describes a broader product vision than the original workout spec.

Implemented or substantially present in code:

- User and role data
- Chapters and locations
- Workout templates, sessions, signups, attendance
- Guide-athlete assignment within sessions
- Equipment CRUD and maintenance
- Certifications and languages
- Weather ingestion and display
- Nova dashboards, filters, metrics, and admin views
- Seeder tooling and large demo-data generation

Partial, inconsistent, or only declarative:

- pairing history and richer athlete-guide matching logic
- public / anonymous workflows beyond limited pieces
- notification and messaging breadth
- export/reporting depth
- HIPAA / ADA / security hardening
- several seeded module records without corresponding models or complete flows

## Important Mismatches

These are known structural problems, not edge cases:

1. Status code mismatch:
   controllers use generic codes like `pending`, `checked_in`, `attended`, `signed_up`, `cancelled`
   seeders define workout/signup scoped codes like `signup_pending`, `signup_checked_in`, `signup_attended`, `session_cancelled`

2. Permissions are effectively open:
   `App\Providers\NovaServiceProvider::gate()` returns `true`
   several policies return `true` for every action

3. Seeded module declarations overstate implementation:
   `WorkoutLocation`, `WorkoutMeetingPoint`, `WorkoutFeedback`, and `WorkoutWeather` are declared in `SystemModuleSeeder`, but those model names do not line up with the actual codebase

4. Public app surface is thin:
   most real behavior lives inside Nova resources, metrics, filters, actions, and buttons

## Deployment Notes

- Laravel Cloud requires a recent Laravel framework lockfile version. This repo is now on `laravel/framework` `11.51.0`.
- Nova is now on `5.8.3`.
- Nova currently supports Laravel `10.x`, `11.x`, and `12.x`, not Laravel `13.x` per the current Nova installation docs.
- For Cloud deployment, configure:
  - `NOVA_LICENSE_KEY`
  - Composer auth for `nova.laravel.com`
  - real `DB_*` values

## First Commands

Use these first when orienting:

```bash
php artisan about
php artisan route:list
php artisan test
```

If local sqlite is the default, create the file first:

```bash
touch database/database.sqlite
```

