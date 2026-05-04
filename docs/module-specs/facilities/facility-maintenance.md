# Facility Maintenance

- Status: `Missing`
- Priority: `P3`
- Depends on: Facilities, Resource Allocation, Safety Incidents

## Purpose
- Track inspections, repairs, and out-of-service windows for facilities.

## Minimum Records
- facility_maintenance with facility_id, issue, due_at, resolved_at, vendor, notes

## Core Workflows
- Log maintenance issues.
- Track closures or restricted access.
- Prevent scheduling during unsafe periods.

## Admin Surface
- Filters: chapter, due soon, unresolved.
- Metrics: open facility issues.
- Lens: upcoming sessions at compromised facilities.

## Implementation Note
- Mirror equipment maintenance patterns once facilities exist.
