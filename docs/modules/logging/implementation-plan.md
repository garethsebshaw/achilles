# Logging Module Implementation Plan

## Goal

Build a tenant-shaped, Nova-visible logging and diagnostics module for Achilles without converting the rest of the application into full multi-tenancy.

The module must:

- reuse Laravel native logging, queue, and exception infrastructure
- add structured operational records for operators and support staff
- remain portable for later extraction into the broader RSC Suite
- avoid unrelated changes to existing Achilles functionality

## Existing Achilles Constraints

- Achilles is currently single-tenant.
- Nova access is role-gated around privileged users.
- The app already has `system_audit_logs`, but it is a thin system-core table and not a full operator diagnostics module.
- The app has no current tenant resolver or tenant model.
- Nova resource authorization defaults are permissive in the shared base resource, so the new logging resources must override visibility explicitly.

## Key Assumptions

1. A minimal `tenants` table is acceptable because the spec explicitly allows it when none exists.
2. Adding `tenant_id` to `users` is the least invasive way to support policy tests and future tenant scoping.
3. Existing Achilles users will be assigned to the default `achilles` tenant.
4. `is_sys_admin` is the platform admin concept.
5. `is_admin` and `is_team_leader` are treated as privileged tenant operators for logging visibility.
6. The first pass will implement queue lifecycle capture for queued, running, succeeded, and failed states using Laravel-native events where practical. `released`, `retried`, and `cancelled` will be supported by the service API and documented if not fully event-driven yet.
7. The first pass will not create `log_retention_policies`; this is deferred unless the existing settings pattern makes it trivial.

## Build Phases

## Phase 1: Foundation

- Create `tenants` table and model.
- Add `tenant_id` to `users`.
- Create `App\Support\Tenancy\CurrentTenant`.
- Add config file `config/rsc_logging.php`.
- Add default tenant seeder.

## Phase 2: Structured Logging Storage

- Create logging tables:
  - `system_logs`
  - `audit_logs`
  - `job_logs`
  - `module_health_checks`
  - `operator_events`
- Create namespaced models under `App\Modules\Logging\Models`.
- Add explicit casts and fillable/guarded rules.

## Phase 3: Services and Sanitization

- Create `LogContextSanitizer`.
- Create:
  - `SystemLogger`
  - `AuditLogger`
  - `JobLogger`
  - `ModuleHealthReporter`
  - `OperatorEventLogger`
- Ensure every service writes safely and never takes the app down if database logging fails.

## Phase 4: Laravel Integration

- Add a dedicated `LoggingServiceProvider`.
- Register queue lifecycle listeners.
- Add safe exception reporting integration.
- Add operator event creation rules for critical logs, failed jobs, and unhealthy modules.

## Phase 5: Nova Operator Surface

- Create Nova resources for:
  - `SystemLog`
  - `AuditLog`
  - `JobLog`
  - `ModuleHealthCheck`
  - `OperatorEvent`
- Add filters, lenses, actions, and metrics.
- Add a Logging dashboard and/or add a Logging section to Nova navigation.
- Keep create/update/delete largely read-only except for acknowledge/resolve-style actions.

## Phase 6: Tests and Documentation

- Add feature and unit tests for:
  - tenant visibility
  - platform visibility
  - redaction
  - audit capture
  - job failure capture
  - health reporting
  - operator event transitions
  - safe fallback on structured-write failure
- Add local-only demo seeder wiring.
- Add release note.

## Explicit Deferrals

- Full retention policy engine
- External observability integrations
- Full custom alerting/notification workflows
- Full application-wide tenancy refactor
- Replacing Laravel native failed job handling
- Replacing Laravel native log channels

## Verification Plan

- `php artisan migrate`
- `php artisan route:list`
- `php artisan test`
- targeted Nova/browser validation for logging resources after implementation
