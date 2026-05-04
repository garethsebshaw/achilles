# Program Sessions

- Status: `Missing`
- Priority: `P2`
- Depends on: Programs, Users, Locations, Attendance patterns

## Purpose
- Model program instances that are not simply weekly workouts, such as clinics, training classes, or orientations.

## Minimum Records
- program_sessions with program_id, location_id, starts_at, ends_at, capacity, status, coordinator

## Core Workflows
- Schedule non-workout program occurrences.
- Manage participation.
- Track completion or attendance.

## Admin Surface
- Filters: program, date window, capacity state.
- Metrics: upcoming program sessions.
- Lens: full or under-staffed sessions.

## Implementation Note
- This is best treated as a sibling pattern to workout sessions, not a duplicate.
