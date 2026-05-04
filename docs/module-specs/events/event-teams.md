# Event Teams

- Status: `Missing`
- Priority: `P3`
- Depends on: Events, Event Registrations, Users, Sports taxonomy

## Purpose
- Organize relay teams, support crews, or multi-person event groupings.

## Minimum Records
- event_teams and event_team_members with event_id, name, role, captain, member order

## Core Workflows
- Create teams from registrations.
- Assign roles and alternates.
- Publish event-day rosters.

## Admin Surface
- Filters: event, team completeness.
- Metrics: full vs partial teams.
- Lens: registrants not assigned to required teams.

## Implementation Note
- Only needed for event formats that actually use teams.
