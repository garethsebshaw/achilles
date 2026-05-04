# Financial Aid

- Status: `Missing`
- Priority: `P3`
- Depends on: Users, Chapters, Events, Programs, Scholarships

## Purpose
- Track need-based assistance decisions for program or event participation.

## Minimum Records
- financial_aid_requests with applicant, chapter, purpose, amount requested, decision, notes

## Core Workflows
- Collect aid requests.
- Review and approve/deny.
- Link grants to event or program payments.

## Admin Surface
- Filters: chapter, aid status, event/program.
- Metrics: aid requested vs approved.
- Lens: approved participants awaiting aid decision.

## Implementation Note
- This is important only if Achilles will replace separate finance or scholarship workflows.
