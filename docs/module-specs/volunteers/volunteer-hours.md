# Volunteer Hours

- Status: `Missing`
- Priority: `P2`
- Depends on: Users, Workouts, Events, Volunteer Programs

## Purpose
- Track contributed volunteer time across workouts, events, logistics, and admin work.

## Minimum Records
- volunteer_hours with user_id, source module, source_id, chapter, hours, approval status

## Core Workflows
- Auto-credit hours from attendance roles when possible.
- Allow manual entries for off-platform service.
- Approve and report on chapter totals.

## Admin Surface
- Filters: chapter, role, month, approved/pending.
- Metrics: volunteer hours this month, top contributors.
- Lens: pending manual hour approvals.

## Implementation Note
- Should reuse session attendance before allowing lots of manual data entry.
