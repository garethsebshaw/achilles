# Transportation

- Status: `Missing`
- Priority: `P2`
- Depends on: Users, Chapters, Locations, Workouts, Events, Accessibility Requirements

## Purpose
- Coordinate rides, pickups, and transport support for workouts and events.

## Minimum Records
- transportation_requests and transportation_assignments with source context, rider, driver, accessibility notes

## Core Workflows
- Request transport support.
- Assign drivers or vans.
- Track pickup status and no-shows.

## Admin Surface
- Filters: chapter, source module, unresolved ride.
- Metrics: rides needed this week.
- Lens: participants missing transport for tomorrow.

## Implementation Note
- Useful for NYC-style chapter operations where transport is part of inclusion.
