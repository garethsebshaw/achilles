# Facilities

- Status: `Missing`
- Priority: `P2`
- Depends on: System Locations, Chapters, Accessibility Requirements, Environmental Conditions

## Purpose
- Track facilities that host programs, workouts, storage, or admin operations with richer metadata than a generic location.

## Minimum Records
- facilities with location_id, usage type, accessibility features, booking notes, capacity, owner

## Core Workflows
- Describe facility capabilities.
- Link facilities to programs and storage.
- Track suitability for accessibility or weather contingencies.

## Admin Surface
- Filters: chapter, facility type, accessibility flag.
- Metrics: accessible facilities by chapter.
- Lens: locations lacking facility profile.

## Implementation Note
- Keep SystemLocation as the shared geography base; Facilities should extend it, not replace it.
