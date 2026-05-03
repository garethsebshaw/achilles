# Achilles Workouts

Laravel 11 / Nova 5 application for Achilles operational workflows.

This repository is broader than the original workout spec. The implemented codebase currently includes:

- Workout templates, sessions, signups, attendance, guide-athlete assignment, meeting points, tandem pairing, and workout-specific details
- Equipment inventory, components, conditions, storage, maintenance requests/logs, and checkouts
- Users, chapters, locations, certifications, languages, tags, audit/system tables, and weather data
- Nova dashboards, metrics, filters, lenses, and custom Nova components for admin workflows

## Current State

This is primarily a Nova-admin application, not a polished public product. Some modules are fully wired through migrations, models, seeders, and Nova resources. Others exist only partially or as seeded module declarations.

Key documents for contributors:

- [AGENTS.md](/Users/garethredfernshaw/.codex/worktrees/20d4/AchillesWorkouts/AGENTS.md)
- [CODEX-START-HERE.md](/Users/garethredfernshaw/.codex/worktrees/20d4/AchillesWorkouts/CODEX-START-HERE.md)

## Local Setup

1. Install PHP dependencies:

```bash
composer install
```

2. Install frontend dependencies:

```bash
npm install
```

3. Create the environment file:

```bash
cp .env.example .env
php artisan key:generate
```

4. Create the SQLite database for local development if you are using the default sqlite config:

```bash
touch database/database.sqlite
php artisan migrate --seed
```

5. Start development services:

```bash
composer run dev
```

## Nova

Nova is required for most of the application surface.

- Runtime license:
  `NOVA_LICENSE_KEY` in `.env` and in Laravel Cloud environment variables
- Composer download auth:
  configure `http-basic.nova.laravel.com` with your Nova account email as the username and the Nova license key as the password

Recommended local Composer auth:

```bash
composer config --global http-basic.nova.laravel.com "your-email@example.com" "your-nova-license-key"
```

Do not rely on committing Composer secrets into source control.

## Laravel Cloud Notes

This repository was updated to a Laravel Cloud-compatible Laravel 11 version.

- `laravel/framework`: `11.51.0`
- `laravel/nova`: `5.8.3`

Before deploying to Laravel Cloud:

1. Set application environment variables from `.env.example`
2. Set `NOVA_LICENSE_KEY`
3. Configure Composer auth for `nova.laravel.com` in the Cloud environment
4. Provision a real database and set `DB_*` values
5. Run migrations during deploy

## Known Risks

- Authorization is extremely permissive in several policies and the Nova gate
- Some controller logic uses status codes that do not match the seeded status codes
- Several seeded modules do not correspond to real models or complete implementations
- The test suite is minimal and was partially scaffold-level before cleanup

