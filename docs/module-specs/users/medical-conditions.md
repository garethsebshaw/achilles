# Medical Conditions

- Status: `Missing`
- Priority: `P1`
- Depends on: Users, Accessibility Requirements, Emergency Contacts, Waivers

## Purpose
- Track declared medical conditions relevant to safe participation and emergency response.

## Minimum Records
- medical_conditions with user_id, condition type, notes, emergency guidance, privacy flags, review date

## Core Workflows
- Capture with consent and visibility controls.
- Surface only to authorized chapter staff when operationally necessary.
- Feed incident and emergency workflows.

## Admin Surface
- Filters: chapter, review due, critical alert present.
- Metrics: users with emergency conditions this week.
- Lens: sessions involving athletes with unmanaged medical notes.

## Implementation Note
- Requires strict authorization, audit logging, and privacy design before implementation.
