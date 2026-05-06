# Logging

## Status

Implemented.

## Purpose

RSC Logging adds a structured, tenant-shaped diagnostics layer on top of Laravel native logging, queue, and exception handling. It is intended for operators, support, and future automation rather than replacing raw Laravel log files.

## Core Surfaces

- `system_logs`
- `audit_logs`
- `job_logs`
- `module_health_checks`
- `operator_events`

## Tenant Shape

- All major logging tables include nullable `tenant_id`.
- Achilles remains effectively single-tenant in behavior.
- A default tenant resolver returns the `achilles` tenant unless explicitly overridden.
- Platform-level records may use `tenant_id = null`.

## Services

- `SystemLogger`
- `AuditLogger`
- `JobLogger`
- `ModuleHealthReporter`
- `OperatorEventLogger`
- `LogContextSanitizer`

## Nova Surface

- read-only logging resources
- logging dashboard
- filters for tenant/date windows
- lenses for recent errors, failed jobs, and unhealthy modules
- operator actions for acknowledge / resolve / ignore workflows

## Security Rules

- sensitive context keys are redacted before structured persistence
- platform admins (`is_sys_admin`) can view all logging records
- non-platform privileged users are limited to their own tenant records
- platform-level records are not shown to non-platform users

## Deferred Items

- retention policy engine
- full alerting / notification workflows
- broad external observability integrations
- full application-wide tenancy refactor
