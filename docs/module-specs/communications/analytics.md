# Analytics

- Status: `Missing`
- Priority: `P2`
- Depends on: Reports, Workouts, Users, Events, Notifications

## Purpose
- Provide reusable aggregate views and KPI definitions across chapters and sports.

## Minimum Records
- analytics_definitions, analytics_snapshots, cached_kpis

## Core Workflows
- Define canonical KPIs.
- Precompute heavy aggregates.
- Drive dashboard cards and strategic reports.

## Admin Surface
- Filters: chapter, program, period.
- Metrics: KPI freshness, data latency.
- Lens: stale analytics caches.

## Implementation Note
- Important for scale. It should come before building dozens of expensive real-time dashboard queries.
