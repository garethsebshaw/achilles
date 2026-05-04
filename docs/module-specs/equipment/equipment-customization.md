# Equipment Customization

- Status: `Missing`
- Priority: `P3`
- Depends on: Equipment, Equipment Components, Maintenance Requests, Equipment Fitting

## Purpose
- Track non-standard modifications made to adaptive equipment for specific athletes or programs.

## Minimum Records
- equipment_customizations with equipment_id, user_id optional, customization type, description, approval status

## Core Workflows
- Request customization from fit or maintenance flows.
- Approve and record build details.
- Link resulting changes to maintenance history.

## Admin Surface
- Filters: approval state, chapter, equipment type.
- Metrics: open customization requests.
- Lens: equipment with undocumented custom modifications.

## Implementation Note
- Should not be built until fitting and maintenance are stable.
