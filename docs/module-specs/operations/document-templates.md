# Document Templates

- Status: `Missing`
- Priority: `P3`
- Depends on: Media Files, Waivers, Event Documents, Reports

## Purpose
- Store versioned templates for waivers, letters, event packets, and generated exports.

## Minimum Records
- document_templates with type, version, file reference, merge variables, active flag

## Core Workflows
- Manage reusable templates.
- Track active vs retired versions.
- Generate populated documents from other modules.

## Admin Surface
- Filters: template type, active, outdated.
- Metrics: templates by module, stale templates.
- Lens: live workflows using retired templates.

## Implementation Note
- Useful infrastructure once waivers or report exports require generated documents.
