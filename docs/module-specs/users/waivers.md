# Waivers

- Status: `Missing`
- Priority: `P1`
- Depends on: Users, Chapters, Programs, Events, Document Templates, Media Files

## Purpose
- Track liability waivers, consent forms, and expiration rules across programs and events.

## Minimum Records
- waivers, waiver_versions, user_waivers with signed_at, expires_at, source channel, file reference

## Core Workflows
- Publish versioned waiver templates.
- Collect signatures.
- Block signups or travel when waivers are missing.

## Admin Surface
- Filters: expired, expiring soon, missing by chapter or event.
- Metrics: waiver compliance by chapter.
- Lens: upcoming participants missing required waivers.

## Implementation Note
- This is one of the first missing modules that should be built.
