# Logging Module Build Checklist

## Phase 1: Docs and Assumptions

- [ ] Confirm Achilles auth and Nova role patterns
- [ ] Confirm queue and logging config patterns
- [ ] Record tenancy assumptions
- [ ] Document deferred items

## Phase 2: Tenant Foundation

- [ ] Create `tenants` table
- [ ] Create `App\Models\Tenant`
- [ ] Add `tenant_id` to `users`
- [ ] Seed default tenant `achilles`
- [ ] Create `App\Support\Tenancy\CurrentTenant`
- [ ] Bind resolver in a provider

## Phase 3: Config

- [ ] Create `config/rsc_logging.php`
- [ ] Add `rsc_structured` channel to `config/logging.php`
- [ ] Keep existing channels intact

## Phase 4: Tables and Models

- [ ] Create `system_logs`
- [ ] Create `audit_logs`
- [ ] Create `job_logs`
- [ ] Create `module_health_checks`
- [ ] Create `operator_events`
- [ ] Create model classes under `App\Modules\Logging\Models`

## Phase 5: Services

- [ ] Create `LogContextSanitizer`
- [ ] Create `SystemLogger`
- [ ] Create `AuditLogger`
- [ ] Create `JobLogger`
- [ ] Create `ModuleHealthReporter`
- [ ] Create `OperatorEventLogger`
- [ ] Ensure structured logging failures fall back to Laravel logs

## Phase 6: Integration

- [ ] Add `LoggingServiceProvider`
- [ ] Register queue event listeners
- [ ] Register safe exception reporting integration
- [ ] Add local demo seeder wiring only for local/dev

## Phase 7: Nova

- [ ] Create `SystemLog` Nova resource
- [ ] Create `AuditLog` Nova resource
- [ ] Create `JobLog` Nova resource
- [ ] Create `ModuleHealthCheck` Nova resource
- [ ] Create `OperatorEvent` Nova resource
- [ ] Add filters
- [ ] Add lenses
- [ ] Add acknowledge/resolve actions
- [ ] Add logging metrics/dashboard
- [ ] Add Nova navigation entries

## Phase 8: Security and Policies

- [ ] Add model policies or resource authorization rules
- [ ] Enforce tenant scoping
- [ ] Enforce platform admin visibility
- [ ] Ensure redaction of sensitive context keys
- [ ] Ensure traces shown in Nova are excerpts only

## Phase 9: Tests

- [ ] Tenant-scoped system logs
- [ ] Platform-level logs
- [ ] Tenant user blocked from other tenant logs
- [ ] Platform admin can view all logs
- [ ] Audit before/after values
- [ ] Secret redaction
- [ ] Job failure capture
- [ ] Health reporter behavior
- [ ] Operator event acknowledge/resolve
- [ ] Structured logging DB failure fallback

## Phase 10: Release and Verification

- [ ] Create `build/releases/logging-module.md`
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan route:list`
- [ ] Run `php artisan test`
- [ ] Validate Nova resources load
