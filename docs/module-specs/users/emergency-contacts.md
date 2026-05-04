# Emergency Contacts

- Status: `Missing`
- Priority: `P1`
- Depends on: Users, System Statuses, optional Chapters

## Purpose
- Store primary and backup emergency contacts for every athlete, guide, volunteer, and staff profile.

## Minimum Records
- emergency_contacts with user_id, contact name, relationship, phones, email, address, notes, is_primary, status_id

## Core Workflows
- Maintain multiple contacts per user with one primary contact.
- Expose contact data in roster, attendance, and incident workflows.
- Support chapter-specific emergency escalation exports.

## Admin Surface
- Filters: chapter, role, missing primary contact, stale > 12 months.
- Metrics: users missing emergency contact, users with only one contact.
- Lens: high-risk athletes with no verified contact.

## Implementation Note
- This is foundational for safety, incidents, and event travel. It should be built before medical or waiver workflows.
