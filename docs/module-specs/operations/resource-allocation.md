# Resource Allocation

- Status: `Missing`
- Priority: `P2`
- Depends on: Chapters, Locations, Equipment, Transportation, Facilities, Events

## Purpose
- Allocate scarce operational resources such as staff, vehicles, adaptive gear, and facility slots.

## Minimum Records
- resource_allocations with source context, resource type, resource_id, chapter, timeslot, priority

## Core Workflows
- Reserve resources for sessions or events.
- Detect conflicts.
- Support reallocation during cancellations or weather changes.

## Admin Surface
- Filters: chapter, resource type, conflict state.
- Metrics: allocation conflicts, resource utilization.
- Lens: upcoming sessions with unresolved resource gaps.

## Implementation Note
- High value once operations spread across many chapters and events.
