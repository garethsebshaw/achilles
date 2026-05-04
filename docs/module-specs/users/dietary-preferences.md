# Dietary Preferences

- Status: `Missing`
- Priority: `P3`
- Depends on: Users, Events, Programs, Categories

## Purpose
- Capture food restrictions and catering preferences for events, travel, and longer training days.

## Minimum Records
- dietary_preferences with user_id, restriction category, freeform notes, severity, chapter visibility

## Core Workflows
- Collect during onboarding and profile edits.
- Surface in event registration, travel, and accommodation planning.
- Allow chapter overrides for one-off event needs.

## Admin Surface
- Filters: chapter, restriction type, severity.
- Metrics: participants requiring special meals per event.
- Lens: upcoming event attendees with dietary needs.

## Implementation Note
- Useful, but not critical before core safety and authorization work.
