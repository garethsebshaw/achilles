# Event Documents

- Status: `Missing`
- Priority: `P3`
- Depends on: Events, Document Templates, Media Files, Waivers

## Purpose
- Manage event packets, waivers, schedules, maps, and post-event recap documents.

## Minimum Records
- event_documents with event_id, document type, file attachment, visibility, expiry

## Core Workflows
- Publish pre-event packets.
- Track required signed docs.
- Archive post-event results or recaps.

## Admin Surface
- Filters: event, required document missing, public/internal.
- Metrics: required docs complete rate.
- Lens: active events missing travel or waiver packets.

## Implementation Note
- Can piggyback on the existing media system once document templates exist.
