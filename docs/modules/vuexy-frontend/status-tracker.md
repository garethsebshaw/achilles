# Vuexy Frontend Status Tracker

## Overall status

Initiative: `Frontend Portal / Vuexy-inspired prototype`

Current phase:
- Phase 2 foundation active, Phase 3 simulation started

## Completed

- Audited current Achilles structure.
- Audited current Nova-heavy architecture.
- Audited local Vuexy package contents.
- Chosen architecture direction:
  - keep Nova
  - add separate frontend surface
  - do not replace Nova directly
- Added `/portal/dashboard` prototype route and controller.
- Added `/portal/dashboard/data` aggregated metrics endpoint.
- Added first portal frontend slice with:
  - live summary cards
  - user registration growth chart
  - workout signup growth chart
  - auto-refresh
  - accessible chart table fallbacks
  - local display preferences for text size, contrast, motion, and density
- Reworked the portal dashboard into a Vuexy-style analytics layout with:
  - hero analytics card
  - compact sparkline cards
  - larger chart cards
  - chapter activity table
  - selected Vuexy illustration assets
- Changed portal refresh behavior so auto refresh is off by default and user-controlled.
- Normalized seeded user and signup history across a one-year window so the charts read like an active system instead of exposing seeder spikes.
- Fixed Blade bootstrap serialization for the frontend config payload.
- Fixed Vite manifest output so the portal renders correctly in the browser.
- Validated the portal slice locally in the browser.
- Added a signed read-only runtime bridge foundation:
  - `/internal/runtime/health`
  - `/internal/runtime/logs`
  - `/internal/runtime/queue-summary`
- Added runtime bridge signature middleware and config.
- Added runtime bridge feature coverage.
- Added a removable demo-activity simulation layer for non-production-style datasets:
  - user registration redistribution across historical windows
  - daily new user generation
  - workout signup redistribution across historical and near-future windows
  - historic session signup backfill
  - rolling future session signup maintenance
- Added the `demo:simulate-activity` command.
- Added scheduler hooks for hourly future-signup maintenance and daily user growth when demo activity is enabled.
- Reworked the portal mount page into a fuller Vuexy-style application shell with:
  - left navigation
  - top navigation bar
  - account/admin shortcuts
  - responsive mobile menu handling
- Validated the simulation command locally and verified the distribution no longer collapses into a single spike day.

## In progress

- expanding the runtime bridge from basic diagnostics into richer development tooling
- preparing the remote rollout for demo-activity redistribution and rolling maintenance

## Not started

- runtime bridge
- codex bridge
- codex bridge
- chapter completeness audit against Achilles International
- athlete/guide portal
- frontend auth/session UX
- ADA remediation plan for frontend surfaces

## Current decisions

- Short-term route: `/portal/dashboard`
- Keep root and Nova behavior unchanged
- Use shared Laravel endpoints/services rather than Nova APIs where practical
- Use real Achilles data for the first dashboard
- Use removable, config-gated simulation tooling for demo growth over time rather than hard-reset seed spikes.
- Accessibility should be handled as a single adaptable interface, not as a separate blind-only site.
- Avoid `nova`, `vuexy`, or `vu` in frontend URLs.
- Keep the frontend architecture compatible with a later `/admin` split without requiring a rewrite.

## Known constraints

- Current repo is Nova-first.
- Vuexy full starter assumes its own app structure and cannot be dropped in wholesale safely.
- The full `vuexy-admin-v10.11.1` download should not be committed.
- Only selected code/assets/patterns should be ported into the main app.

## Restart notes

If work stops unexpectedly, restart from:
1. `docs/modules/vuexy-frontend/status-tracker.md`
2. `docs/modules/vuexy-frontend/implementation-plan.md`
3. `docs/modules/vuexy-frontend/worklog.md`

Then continue with the next unchecked item in the worklog.
