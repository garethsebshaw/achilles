# Expense Tracking

- Status: `Missing`
- Priority: `P3`
- Depends on: Events, Programs, Resource Allocation, Sponsorships

## Purpose
- Track non-GL operational expenses tied to programs, events, travel, and equipment support.

## Minimum Records
- expenses with chapter, source module, vendor, amount, category, reimbursement state

## Core Workflows
- Log expenses.
- Tie them to events or programs.
- Support reimbursement and sponsor reporting.

## Admin Surface
- Filters: chapter, category, reimbursement due.
- Metrics: expenses by month/program.
- Lens: uncategorized or unapproved expenses.

## Implementation Note
- Only worth building if no external finance tool is the source of truth.
