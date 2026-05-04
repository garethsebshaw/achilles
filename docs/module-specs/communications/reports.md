# Reports

- Status: `Missing`
- Priority: `P2`
- Depends on: Core data domains, Analytics, Chapters, Authorization

## Purpose
- Store saved report definitions and support repeatable operational exports.

## Minimum Records
- reports with owner, chapter scope, query config, schedule, output format

## Core Workflows
- Save parameterized reports.
- Run ad hoc or scheduled exports.
- Share report definitions with chapter admins.

## Admin Surface
- Filters: owner, chapter, scheduled/not scheduled.
- Metrics: most-run reports, failed report jobs.
- Lens: reports using deprecated fields.

## Implementation Note
- This should build on a reporting service layer, not raw SQL pasted into Nova.
