# Environmental Conditions

- Status: `Missing`
- Priority: `P2`
- Depends on: Facilities, Weather data, Activity Risk, Programs

## Purpose
- Track non-weather environmental factors such as air quality, heat risk, water condition, or indoor constraints.

## Minimum Records
- environmental_conditions with source location/facility, condition type, measured_at, severity, notes

## Core Workflows
- Record environment advisories.
- Influence session go/no-go decisions.
- Feed risk dashboards.

## Admin Surface
- Filters: location, condition type, active advisory.
- Metrics: active environmental advisories.
- Lens: sessions scheduled during high-risk conditions.

## Implementation Note
- Design this as a supplement to weather, not a competing weather store.
