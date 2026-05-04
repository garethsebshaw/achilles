# Safety Incidents

- Status: `Missing`
- Priority: `P1`
- Depends on: Users, Workouts, Sessions, Events, Locations, Emergency Contacts

## Purpose
- Provide a formal incident reporting and follow-up system for injuries, near misses, and safeguarding issues.

## Minimum Records
- safety_incidents with source module, location, people involved, severity, narrative, follow-up status

## Core Workflows
- Open incident from session or event context.
- Assign investigation and remedial actions.
- Escalate critical issues immediately.

## Admin Surface
- Filters: chapter, severity, open/closed, incident type.
- Metrics: incidents by month and severity.
- Lens: open incidents overdue for review.

## Implementation Note
- This is one of the highest-priority missing modules for operational maturity.
