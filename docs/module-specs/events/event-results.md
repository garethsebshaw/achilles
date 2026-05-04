# Event Results

- Status: `Missing`
- Priority: `P3`
- Depends on: Events, Event Registrations, Event Teams, Users

## Purpose
- Capture outcomes, times, placements, and milestone achievements from events.

## Minimum Records
- event_results with event_id, registration_id or team_id, outcome type, rank, time, notes

## Core Workflows
- Record official and unofficial results.
- Tie achievements back into user history.
- Support export to recognition and reporting flows.

## Admin Surface
- Filters: event, result status, podium/placement.
- Metrics: finishers, podiums, PRs.
- Lens: published events with no result records.

## Implementation Note
- Results should feed achievements rather than duplicating it.
