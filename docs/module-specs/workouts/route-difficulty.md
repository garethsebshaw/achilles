# Route Difficulty

- Status: `Missing`
- Priority: `P2`
- Depends on: Workouts, System Locations, Accessibility Requirements, Programs

## Purpose
- Normalize workout route difficulty so chapters can plan inclusive sessions consistently.

## Minimum Records
- route_difficulties and workout_route_profiles with grade, descriptors, elevation, terrain, accessibility flags

## Core Workflows
- Attach difficulty profile to workouts and sessions.
- Match athletes/guides to appropriate routes.
- Warn when a signup exceeds supported difficulty.

## Admin Surface
- Filters: sport, difficulty, chapter.
- Metrics: sessions by difficulty band.
- Lens: athletes signed into unsupported route levels.

## Implementation Note
- Should be designed as taxonomy plus per-workout profile, not just a flat lookup.
