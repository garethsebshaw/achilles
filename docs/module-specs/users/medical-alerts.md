# Medical Alerts

- Status: `Missing`
- Priority: `P1`
- Depends on: Medical Conditions, Users, Notifications

## Purpose
- Represent concise operational alerts such as seizure risk, allergy, or emergency medication requirement.

## Minimum Records
- medical_alerts with user_id, alert text, severity, operational instructions, active flag

## Core Workflows
- Derive alerts from medical conditions where appropriate.
- Display alerts only in context for authorized staff.
- Track acknowledgement when viewed during check-in.

## Admin Surface
- Filters: severity, active, chapter.
- Metrics: active medical alerts by chapter.
- Lens: users with active alert but no recent review.

## Implementation Note
- Should be a derived, tightly permissioned operational view rather than a completely separate medical system.
