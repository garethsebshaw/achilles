# Medications

- Status: `Missing`
- Priority: `P2`
- Depends on: Medical Conditions, Users, Medical Alerts

## Purpose
- Track participant medications only when operationally necessary for events or emergency support.

## Minimum Records
- medications with user_id, medication name, dosage notes, administration notes, emergency relevance

## Core Workflows
- Capture limited medication data with consent.
- Link relevant meds to alerts or event travel plans.
- Avoid exposing details broadly in Nova.

## Admin Surface
- Filters: emergency relevant, review due.
- Metrics: users carrying rescue medication.
- Lens: events with attendees needing med storage support.

## Implementation Note
- Lower priority than medical conditions and alerts, and should be privacy-scoped aggressively.
