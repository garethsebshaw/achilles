# Accessibility Requirements

- Status: `Missing`
- Priority: `P1`
- Depends on: Users, Chapters, Locations, Programs, Categories

## Purpose
- Track accommodations required for communication, transport, equipment setup, and venue access.

## Minimum Records
- accessibility_requirements with user_id, requirement type, description, severity, chapter scope, status_id

## Core Workflows
- Capture during onboarding and chapter intake.
- Reference in session planning, event planning, and location suitability checks.
- Alert check-in staff when a session lacks required support.

## Admin Surface
- Filters: chapter, accommodation type, unresolved support gap.
- Metrics: users requiring accommodations this week.
- Lens: sessions with unmet accessibility requirements.

## Implementation Note
- This is first-class for an adaptive sports system and should precede many event and facility modules.
