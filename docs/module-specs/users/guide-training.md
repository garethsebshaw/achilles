# Guide Training

- Status: `Missing`
- Priority: `P2`
- Depends on: Users, Certifications, Qualification Verification, Programs

## Purpose
- Track structured guide training courses, completions, refreshers, and chapter-level readiness.

## Minimum Records
- guide_trainings and guide_training_completions with chapter, course, trainer, completed_at, expires_at

## Core Workflows
- Publish chapter training cohorts.
- Enroll guides, track completion, and block assignments when training is incomplete.
- Trigger renewal reminders.

## Admin Surface
- Filters: chapter, course, expired soon, incomplete.
- Metrics: trained guides by chapter and sport.
- Lens: upcoming sessions assigned to untrained guides.

## Implementation Note
- Pairs naturally with qualification verification and should be chapter-scoped from day one.
