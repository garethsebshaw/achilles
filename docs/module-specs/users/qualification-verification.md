# Qualification Verification

- Status: `Missing`
- Priority: `P1`
- Depends on: Users, Certifications, Guide Training, Background check provider integration

## Purpose
- Store verification outcomes for certifications, references, background checks, and other role prerequisites.

## Minimum Records
- qualification_verifications with user_id, requirement type, provider, verified_at, expires_at, evidence link, status_id

## Core Workflows
- Evaluate readiness for guide or staff roles.
- Ingest external verification results.
- Block assignments when checks are missing or expired.

## Admin Surface
- Filters: requirement type, expired soon, failed/pending.
- Metrics: eligible vs blocked guides/staff.
- Lens: assigned users missing required verification.

## Implementation Note
- This is the correct home for Sterling/background-check outcomes and should be built early.
