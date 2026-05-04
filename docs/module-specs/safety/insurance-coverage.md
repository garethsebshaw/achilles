# Insurance Coverage

- Status: `Missing`
- Priority: `P2`
- Depends on: Users, Chapters, Events, Waivers, Safety Incidents

## Purpose
- Track policy coverage, participant eligibility, and documentation needed for claims or compliance.

## Minimum Records
- insurance_coverages with policy holder, provider, coverage type, effective dates, attached docs

## Core Workflows
- Check coverage before high-risk events.
- Support incident follow-up and claim preparation.
- Track missing or expired documentation.

## Admin Surface
- Filters: chapter, coverage type, expired soon.
- Metrics: uninsured participants or events.
- Lens: incidents involving unclear coverage.

## Implementation Note
- This should be limited to administrative tracking unless the org explicitly wants full policy management.
