# Event Registrations

- Status: `Missing`
- Priority: `P2`
- Depends on: Events, Users, Chapters, Waivers, Payments

## Purpose
- Manage athlete, guide, staff, and volunteer registrations for events beyond weekly workouts.

## Minimum Records
- event_registrations with event_id, user_id, role, status, payment state, waiver state, notes

## Core Workflows
- Open/close registration windows.
- Manage waitlists and approvals.
- Trigger downstream travel, lodging, and equipment workflows.

## Admin Surface
- Filters: event, chapter, role, waitlisted, incomplete registration.
- Metrics: registered vs capacity, incomplete registrations.
- Lens: approved attendees missing prerequisites.

## Implementation Note
- This is the core event submodule. Build it before any other event adjuncts.
