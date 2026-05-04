# AGENTS.md

Before making code changes in this repository, read [CODEX-START-HERE.md](/Users/garethredfernshaw/.codex/worktrees/20d4/AchillesWorkouts/CODEX-START-HERE.md).

## Repo Rules

- Treat this as a Nova-first Laravel admin application.
- Prefer reading migrations, Nova resources, seeders, and routes before trusting prose docs.
- Do not store new secrets in tracked files. Prefer `.env`, Laravel Cloud environment variables, or Composer global / CI auth configuration.
- If touching workout attendance, verify both the API controller flow and the Nova button / event flow.
- If touching statuses or categories, verify the matching seeders in `database/seeders`.
- If touching deployment or package versions, verify Laravel Cloud compatibility and Nova compatibility first.
- Any user-facing copy change must be wired through the locale system. Maintain `lang/` files alongside the code change, keep English complete, and update all supported app locales that still make sense for the feature surface.
- During development, if Laravel Cloud ends up with an incomplete or divergent seeded/demo dataset after rebuild or deploy, you may perform a full remote database reset and reseed without asking again. Stop doing that only once the user explicitly says the system is live or that production data must be preserved.

## Verification Expectations

- Run `php artisan route:list` after route or controller changes.
- Run `php artisan test` after PHP changes when feasible.
- Call out mismatches between seeded data and controller logic instead of silently coding around them.

## Current Hotspots

- `routes/web.php`: public and resource route wiring
- `app/Providers/NovaServiceProvider.php`: Nova menu, gate, route behavior
- `app/Http/Controllers/Api/WorkoutSessionAttendanceController.php`: attendance and pairing logic
- `database/seeders/SystemModuleSeeder.php`, `SystemStatusSeeder.php`, `SystemCategorySeeder.php`: declared module surface and workflow vocabulary
