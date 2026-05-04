# Vehicle Maintenance

- Status: `Missing`
- Priority: `P3`
- Depends on: Vehicles, Maintenance patterns, Insurance Coverage

## Purpose
- Track service history, inspection schedules, and out-of-service periods for vehicles.

## Minimum Records
- vehicle_maintenance with vehicle_id, service type, due_at, completed_at, vendor, notes

## Core Workflows
- Schedule inspections and service.
- Block transport assignment when out of service.
- Preserve maintenance history.

## Admin Surface
- Filters: due soon, overdue, chapter.
- Metrics: fleet service backlog.
- Lens: assigned vehicles overdue for maintenance.

## Implementation Note
- Mirror the equipment maintenance pattern once vehicle management exists.
