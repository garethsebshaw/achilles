# Content Moderation

- Status: `Missing`
- Priority: `P4`
- Depends on: Communications, Media Files, Workout Feedback

## Purpose
- Moderate user-generated or community-submitted content before broad publication.

## Minimum Records
- content_moderation_queue with source type, content reference, reason, reviewer, resolution

## Core Workflows
- Queue risky content.
- Review and approve/reject.
- Escalate safeguarding concerns.

## Admin Surface
- Filters: source type, unresolved, escalation needed.
- Metrics: moderation backlog.
- Lens: content awaiting urgent review.

## Implementation Note
- Do not build unless the platform will surface free-form community content.
