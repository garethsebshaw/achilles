# Communications

- Status: `Missing`
- Priority: `P2`
- Depends on: Users, Chapters, Notifications, Programs, Workouts, Events

## Purpose
- Provide governed outbound communication campaigns, audience segmentation, and message logs.

## Minimum Records
- communications, communication_audiences, communication_deliveries with channel, content, target rules, send status

## Core Workflows
- Compose chapter or program messages.
- Target recipients by role, chapter, and status.
- Track delivery and acknowledgement.

## Admin Surface
- Filters: channel, chapter, campaign status.
- Metrics: sends, opens, acknowledgements.
- Lens: failed or unsent critical communications.

## Implementation Note
- This should be provider-agnostic and can later integrate email, SMS, push, and Slack/Teams.
