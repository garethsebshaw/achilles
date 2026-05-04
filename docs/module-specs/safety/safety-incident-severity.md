# Safety Incident Severity

- Status: `Missing`
- Priority: `P1`
- Depends on: Safety Incidents, System Categories, Statuses

## Purpose
- Normalize incident severity so chapters classify events consistently and route escalations correctly.

## Minimum Records
- safety_incident_severities with severity level, response target, notification rules

## Core Workflows
- Drive incident escalation deadlines.
- Support analytics and cross-chapter comparison.
- Link into notification rules.

## Admin Surface
- Filters: severity active/inactive.
- Metrics: incidents by severity.
- Lens: severity levels with no response policy.

## Implementation Note
- This is best modeled as a taxonomy table plus policy metadata.
