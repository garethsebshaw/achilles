# Event Equipment

- Status: `Missing`
- Priority: `P3`
- Depends on: Events, Event Registrations, Equipment, Equipment Loans, Equipment Checkouts

## Purpose
- Reserve and assign equipment for events that require travel or multi-day support.

## Minimum Records
- event_equipment_assignments with event_id, user_id optional, equipment_id, transport status, return status

## Core Workflows
- Reserve equipment before travel.
- Track packing, shipping, pickup, and return.
- Coordinate with long-term loans when needed.

## Admin Surface
- Filters: event, equipment type, missing assignment.
- Metrics: reserved vs missing equipment.
- Lens: equipment assigned to overlapping events.

## Implementation Note
- Do not build until event registrations and travel exist.
