# Care Partners

- Status: `Missing`
- Priority: `P2`
- Depends on: Users, Assistance Requirements, Emergency Contacts

## Purpose
- Manage non-staff support people who assist a participant during travel, events, or sessions.

## Minimum Records
- care_partners with participant_user_id, partner_user_id or external contact, scope, notes, approval state

## Core Workflows
- Register care partners.
- Authorize event/session access.
- Coordinate with travel and accommodation workflows.

## Admin Surface
- Filters: chapter, event, active/inactive.
- Metrics: participants with active care partners.
- Lens: travel attendees needing care partner assignment.

## Implementation Note
- Make room for external non-user contacts as well as users in the system.
