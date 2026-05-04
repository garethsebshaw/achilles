# Guide Experience

- Status: `Missing`
- Priority: `P2`
- Depends on: Users, Workouts, Sessions, Sports taxonomy, Guide Training

## Purpose
- Model guide experience by sport, skill level, and athlete support profile.

## Minimum Records
- guide_experiences with user_id, sport category, level, notes, approved_by, approved_at

## Core Workflows
- Record chapter-approved experience profiles.
- Use during guide-athlete pairing and signup recommendations.
- Track progression from trainee to lead guide.

## Admin Surface
- Filters: chapter, sport, experience level.
- Metrics: experienced guides per sport.
- Lens: high-support athletes without matching guides.

## Implementation Note
- This should reuse workout history rather than forcing staff to enter everything manually.
