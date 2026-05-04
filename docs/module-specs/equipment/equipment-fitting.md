# Equipment Fitting

- Status: `Missing`
- Priority: `P2`
- Depends on: Equipment, Equipment Components, Users, Accessibility Requirements, Locations

## Purpose
- Track user-to-equipment fit profiles for adaptive devices, tandem setups, and sizing constraints.

## Minimum Records
- equipment_fittings with user_id, equipment_id or type, fit measurements, approved_by, review_due_at

## Core Workflows
- Record fit sessions and measurement history.
- Reference during equipment assignment and checkouts.
- Flag sessions where assigned equipment is out of fit tolerance.

## Admin Surface
- Filters: sport, chapter, expired fitting.
- Metrics: athletes missing fit profile.
- Lens: upcoming assignments without approved fit.

## Implementation Note
- High operational value once equipment assignments are relied on in production.
