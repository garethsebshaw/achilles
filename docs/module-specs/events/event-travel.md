# Event Travel

- Status: `Missing`
- Priority: `P3`
- Depends on: Events, Event Registrations, Users, Chapters

## Purpose
- Track transport plans, arrivals, departures, and travel assistance for event attendees.

## Minimum Records
- event_travel with registration_id, itinerary, carrier, booking refs, support needs, cost center

## Core Workflows
- Collect itinerary data.
- Coordinate staff pickups and accessibility assistance.
- Reconcile travel plans against attendance.

## Admin Surface
- Filters: event, travel status, accessibility assistance.
- Metrics: travelers by event and arrival day.
- Lens: attendees with incomplete itineraries.

## Implementation Note
- Should share traveler identity and accessibility data rather than duplicating profile fields.
