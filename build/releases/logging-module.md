# Logging Module Release Note

## What Was Added

- Minimal tenant foundation for logging portability:
  - `tenants` table
  - `App\Models\Tenant`
  - `App\Support\Tenancy\CurrentTenant`
  - `tenant_id` on `users`
- Structured logging tables:
  - `system_logs`
  - `audit_logs`
  - `job_logs`
  - `module_health_checks`
  - `operator_events`
- Logging services under `App\Modules\Logging\Services`
- Sanitizer and exception reporter support under `App\Modules\Logging\Support`
- Queue lifecycle integration through `LoggingServiceProvider`
- Nova logging resources, filters, lenses, metrics, actions, and dashboard
- Local-only logging demo seeder

## Tenant-Shaped Decisions

- Achilles remains operationally single-tenant.
- Logging storage is tenant-shaped for future extraction into the broader RSC Suite.
- Platform-level records are supported via `tenant_id = null`.
- Existing users are assigned to the default `achilles` tenant through seeding.

## Laravel / Nova Native Functionality Reused

- Laravel logging channels and Monolog-backed `Log` facade
- Laravel queue lifecycle events and existing `jobs` / `failed_jobs` tables
- Laravel exception reporting pipeline
- Nova resources
- Nova filters
- Nova lenses
- Nova metrics
- Nova actions
- Nova dashboards

## Security / Redaction Behavior

- Sensitive context keys are redacted before structured persistence.
- Platform admins use `is_sys_admin`.
- Tenant-scoped privileged visibility uses existing privileged Achilles roles.
- Platform-level log records are not exposed to non-platform users.
- Exception traces stored in structured logs are excerpted, not full raw traces.

## Tests Added

- default tenant creation
- tenant-scoped vs platform-level system logs
- tenant visibility restrictions
- audit before/after capture and redaction
- sanitizer redaction
- job failure capture
- module health reporting
- operator event acknowledge/resolve
- safe fallback when structured DB logging fails

## Deferred Items

- retention policy engine
- external observability integrations
- broad alerting / notification workflows
- full application-wide tenancy refactor
- richer retry / release / cancel queue state automation
- deeper export and operator workflow tooling beyond the first Nova actions
