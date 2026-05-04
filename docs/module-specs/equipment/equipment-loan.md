# Equipment Loan

- Status: `Missing`
- Priority: `P3`
- Depends on: Equipment Checkouts, Users, Chapters, Waivers

## Purpose
- Handle longer-term loans that need stronger approval, tracking, and return workflows than same-day checkouts.

## Minimum Records
- equipment_loans with equipment_id, borrower_id, issued_by, due_at, returned_at, agreement reference

## Core Workflows
- Approve loan requests.
- Generate loan agreements and reminders.
- Escalate overdue returns by chapter.

## Admin Surface
- Filters: overdue, chapter, equipment type.
- Metrics: active loans, overdue loans.
- Lens: high-value assets currently loaned out.

## Implementation Note
- Rename the module row from `Equipment Loan ???` before implementation.
