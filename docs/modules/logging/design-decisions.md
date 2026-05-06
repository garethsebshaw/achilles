# Logging Module Design Decisions

## 1. Laravel Logging Stays Primary

This module does not replace Laravel logging.

Laravel and Monolog remain the raw developer logging layer. Structured database logging is added only for records that operators, support, and future automation need to query inside the app.

## 2. Tenant-Shaped, Not Tenant-Refactored

Achilles remains effectively single-tenant in behavior, but the logging module is built with nullable `tenant_id` fields and a `CurrentTenant` resolver so it can be extracted later.

This is intentionally narrow:

- add a minimal `tenants` table
- add `tenant_id` to `users`
- do not rewrite the rest of Achilles around tenancy

## 3. Default Tenant Strategy

All existing Achilles users and tenant-scoped logging records will resolve to the default tenant:

- `key`: `achilles`
- `name`: `Achilles`

Platform-level records may explicitly set `tenant_id = null`.

## 4. Least-Invasive Platform Admin Rule

The app already uses `is_sys_admin` and `is_admin`.

For this module:

- `is_sys_admin` = platform admin
- `is_admin` and `is_team_leader` = privileged tenant operators
- non-privileged users should not get broad logging access

## 5. New Logging Tables Do Not Replace Existing `system_audit_logs`

Achilles already has `system_audit_logs`, but it is lightweight system-core history, not a full RSC logging module.

The new `audit_logs` table is created separately so the logging module remains spec-compliant and portable.

## 6. Safe Failure Path Is Mandatory

Structured logging must never become a point of failure.

If a database write fails:

- Laravel log write should still happen
- the module should emit a fallback Laravel error
- no fatal exception should propagate from the logging service itself

## 7. Exception Reporting Must Be Selective

The module will integrate with Laravel exception reporting, but it should avoid noisy duplicates.

The first implementation will:

- skip low-value spammy exception types where reasonable
- record summarized exception data
- store only a trace excerpt in database records
- never store secrets or raw sensitive request payloads

## 8. Nova Is the Operator UI

Nova is the right first operator surface for this module because it already gives:

- filters
- lenses
- metrics
- searchable resources
- read-only resource views

The module should not build a parallel admin UI for pass one.

## 9. Retention Policy Is Deferred

The module spec allows retention policy to be optional. It is deferred here because Achilles does not yet have a sufficiently mature cross-cutting settings engine for safe retention management.

## 10. Queue Logging Sits Beside Laravel Queue Tables

`job_logs` is not a replacement for `jobs` or `failed_jobs`.

It exists to provide operator-friendly state, duration, summarized payload context, and Nova visibility.
