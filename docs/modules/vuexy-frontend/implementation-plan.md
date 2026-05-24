# Vuexy Frontend Implementation Plan

## Objective

Add a polished frontend surface alongside Nova without destabilizing the existing Achilles admin system.

Nova remains the operational backend and fallback debugging/admin plane.

Vuexy becomes a separate frontend experience for:
- stakeholder demos
- athlete/guide/member-facing workflows
- richer analytics and reporting
- future multi-tenant product UX patterns for RSC Suite

## Recommended Architecture

### Near term

- Keep Nova at its current path and behavior.
- Add a separate frontend surface under `/portal` for prototyping.
- Keep the root path and Nova unchanged until the frontend proves itself.
- Build the frontend against shared Laravel models/services/API endpoints rather than Nova internals.

### Long term

For RSC Suite, the best structure is:
- `/admin` -> Nova
- `/app` or `/portal` -> product frontend
- shared domain/services layer underneath both

For Achilles right now, the lowest-risk path is:
- `/dashboards/main` stays Nova
- `/portal/dashboard` becomes the first frontend slice

## Why not replace Nova directly

- Nova is already the reliable operator surface.
- Heavy restyling of Nova creates upgrade pain.
- Tenant/user-facing workflows need a product frontend, not just an admin shell.
- Accessibility, mobile UX, analytics, kanban, and richer dashboards are better handled outside Nova.

## Phase Breakdown

### Phase 0: Tracking and architecture

- Create work tracking docs.
- Define route strategy.
- Define naming:
  - `VU` = Vuexy frontend prototype surface
  - `Nova` = backend/admin/operator plane
- Define what data comes from shared APIs, not Nova.

### Phase 1: Thin vertical slice

- Add `/portal/dashboard`.
- Render a starter dashboard using real Achilles data.
- Expose two live charts:
  - user registrations over time
  - workout signups over time
- Add headline cards:
  - users
  - chapters
  - sessions
  - signups
- Add refresh behavior so the page updates without a full reload.
- Add accessibility-first structure:
  - semantic sections
  - skip links
  - readable chart alternatives
  - user display preferences
  - reduced-motion support

### Phase 2: Runtime and Codex bridges

- Add a signed runtime bridge for:
  - app health
  - recent logs
  - queue summary
  - module health
  - version/build info
- Add a signed audited Codex bridge for:
  - diagnostic bundles
  - deeper support exports
- Keep this separate from Nova.
- Start with read-only health and diagnostics first so it immediately helps development and debugging without introducing deployment-risky mutation paths.
- Require explicit shared-secret configuration through environment variables; do not hardcode bridge secrets into the repository.

### Phase 3: Demo data simulation module

- Build a removable simulation module that:
  - backfills workout signups
  - future-fills signups probabilistically
  - respects event/session timing
  - can be turned off or removed cleanly
- Use Laravel scheduler/queue, not ad hoc scripts.

### Phase 4: Chapter and location completeness

- Verify Achilles International chapter list against the live source.
- Reconcile missing chapters/locations in seeders/data sync.
- Ensure associated sessions, athletes, and guides exist.

### Phase 5: Frontend expansion

- athlete/guide portal
- chapter dashboard
- richer analytics/reports
- possibly kanban/support workflow pages

## Route Strategy

### Current recommended route map

- `/login` -> existing auth
- `/dashboards/main` -> Nova
- `/portal/dashboard` -> prototype frontend

### Future recommended route map

- `/admin` -> Nova
- `/app` or `/portal` -> product frontend
- `/` -> marketing or landing

Do not move Nova to `/admin` yet in this first slice.

## ADA / Accessibility

Nova should not be assumed fully ADA compliant out of the box.

Expected reality:
- Nova is serviceable for internal admin use
- custom tenant/user-facing experiences should target WCAG 2.2 AA directly

Best path:
- keep Nova for internal admin
- build accessible frontend workflows in the custom frontend

### Prototype compliance strategy

Build a single adaptable interface rather than separate blind-only pages:
- semantic document structure
- visible focus states
- chart companion tables
- user-controlled text size
- high-contrast mode
- reduced-motion mode
- density/layout preferences

These should be implemented so they can later move from local/session preferences into saved per-user profile preferences without redesigning the UI.

## Data and API Principles

- Frontend reads from shared Laravel endpoints/services.
- No scraping Nova pages.
- No duplicating Nova logic unless it becomes domain logic in services.
- Metrics/charts should come from dedicated aggregated endpoints.

## Immediate Deliverables

- tracking docs
- starter `/portal/dashboard`
- live signup growth charts
- restart-safe worklog
- a path to start the runtime bridge next without refactoring the portal slice

## Deferred for later phases

- full Vuexy package integration with all its dependencies
- guide/athlete CRUD portal
- runtime bridge implementation
- codex bridge implementation
- simulation/autofill module
- chapter sync automation
