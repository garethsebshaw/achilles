# Event Payments

- Status: `Missing`
- Priority: `P3`
- Depends on: Event Registrations, Financial Aid, Expense Tracking

## Purpose
- Track registration fees, reimbursements, scholarships, and outstanding balances for events.

## Minimum Records
- event_payments with registration_id, payer, amount, currency, status, reimbursement flag

## Core Workflows
- Collect or waive fees.
- Track reimbursements and sponsorship offsets.
- Surface unpaid registrations before cutoffs.

## Admin Surface
- Filters: event, paid/unpaid, reimbursement due.
- Metrics: collected fees, outstanding balances.
- Lens: approved travelers with unpaid registration.

## Implementation Note
- Hold until the organization decides whether the system will own real payments or just administrative tracking.
