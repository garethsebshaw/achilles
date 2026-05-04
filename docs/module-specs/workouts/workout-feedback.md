# Workout Feedback

- Status: `Missing`
- Priority: `P3`
- Depends on: Workout Sessions, Workout Signups, Users, Notifications

## Purpose
- Collect post-session feedback from athletes, guides, and chapter staff.

## Minimum Records
- workout_feedback with workout_session_id, user_id, sentiment, ratings, narrative feedback, follow_up_required

## Core Workflows
- Prompt after attendance is completed.
- Allow staff follow-up and issue escalation.
- Aggregate trends by chapter, sport, and coach/lead.

## Admin Surface
- Filters: chapter, sport, negative feedback, unresolved follow-up.
- Metrics: satisfaction trend, follow-up backlog.
- Lens: sessions with repeated feedback issues.

## Implementation Note
- Useful after attendance, notifications, and role scoping are settled.
