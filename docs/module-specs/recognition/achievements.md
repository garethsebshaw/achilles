# Achievements

- Status: `Missing`
- Priority: `P3`
- Depends on: Users, Workouts, Events, Volunteer Hours, Event Results

## Purpose
- Track earned milestones such as attendance streaks, races completed, and volunteer service thresholds.

## Minimum Records
- achievements and user_achievements with criteria source, achieved_at, visibility

## Core Workflows
- Calculate achievements from real activity.
- Allow manual award when needed.
- Feed dashboards and recognition flows.

## Admin Surface
- Filters: achievement type, chapter, recently earned.
- Metrics: achievements awarded this month.
- Lens: candidates pending recalculation.

## Implementation Note
- This should be metrics-driven, not hand-entered for every user.
