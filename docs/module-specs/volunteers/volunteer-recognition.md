# Volunteer Recognition

- Status: `Missing`
- Priority: `P3`
- Depends on: Volunteer Hours, Achievements, Awards, Notifications

## Purpose
- Manage service milestones, chapter recognition, and acknowledgement workflows.

## Minimum Records
- volunteer_recognition with user_id, recognition type, awarded_at, chapter, source metric

## Core Workflows
- Generate milestone candidates from hours and attendance.
- Approve chapter recognitions.
- Notify volunteers and support ceremonies.

## Admin Surface
- Filters: chapter, recognition type, pending approval.
- Metrics: milestones reached this quarter.
- Lens: volunteers nearing a recognition threshold.

## Implementation Note
- Should be automation-driven, not an isolated CRUD table.
