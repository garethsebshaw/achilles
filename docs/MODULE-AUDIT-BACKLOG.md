# Achilles Module Audit Backlog

## Purpose

This document turns the current codebase review into a practical cleanup and product-hardening backlog for a multi-country, multi-lingual, multi-sport volunteer operations system.

The goal is not only to remove bugs, but to shape Achilles into a first-class internal platform for:

- athletes
- guides
- check-in staff
- chapter leaders
- local admins
- regional admins
- system admins

## Audit Method

For each module, the review should validate the full stack:

1. Schema
   - migrations match the current model fields
   - foreign keys and indexes fit real query patterns
   - soft delete behavior is intentional and consistent
2. Model layer
   - relationships are correct
   - casts and accessors do not fight the schema
   - scopes exist for the operational queries staff actually need
3. Nova/admin surface
   - index queries are scoped and performant
   - fields use the correct module statuses/categories
   - actions, filters, lenses, and cards match real workflows
4. Controllers / API / commands
   - vocabulary matches seeded statuses and categories
   - writes are safe under overlapping sessions, chapters, and roles
   - background jobs and scheduled commands are idempotent
5. Seeders / demo data
   - seeded data is realistic enough for workflow testing
   - seeded references line up with the actual implementation
6. Locale / copy
   - all user-facing strings are localized
   - terminology is consistent across sports, roles, and attendance workflows
7. Performance / authorization
   - large-list defaults are scoped
   - search is constrained
   - non-admin users cannot see operational data outside their scope

## Reality Check

The `system_modules` table contains `103` module records, but not all are truly implemented. Some are fully real. Some are partial. Some are declarative stubs.

Work should proceed by audit domains, not by every module row in isolation.

## Recommended Priority Order

### P0: Access, Scope, and Operational Safety

These directly affect correctness, privacy, and whether staff can safely operate at scale.

1. Authentication, authorization, and chapter context
2. Workouts, sessions, signups, attendance, pairing
3. Geography and location correctness
4. Query scoping and large-data defaults

### P1: Core Platform Reliability

These are foundational system behaviors.

5. System/core modules
6. Users, chapter-role assignments, location access
7. Weather live data and weather operations

### P2: Operational Depth

These matter to day-to-day staff usage after P0/P1 are stable.

8. Equipment and maintenance
9. Certifications and languages
10. Events
11. Notifications

### P3: Product Expansion / Vision Modules

These should be deferred until the implemented core is stable.

12. Declarative / not-yet-real modules from `system_modules`
13. Reporting, sponsorship, finance, awards, programs, medical, transport, and other adjacent surfaces

## Module Domains

## 1. Auth, Roles, and Scope

### Priority
P0

### Why
The system cannot scale safely without chapter-scoped access and role-aware surfaces. This also controls performance.

### Current Direction
- global booleans exist on `users`
- location access exists
- Nova access is role-gated, but not yet chapter-scoped
- athlete/guide access model needs to diverge from admin usage

### Validation Steps
1. Audit all access decisions in:
   - `app/Providers/NovaServiceProvider.php`
   - `app/Nova/Resource.php`
   - `app/Policies/*`
   - `app/Models/User.php`
2. Confirm which resources should be:
   - global admin only
   - chapter-scoped staff only
   - self-only
3. Design and implement chapter-role assignment data model:
   - `user_chapter_roles`
   - optional later: sport scope, location scope, time-bounded assignments
4. Add active chapter context at login / session time for non-global users
5. Make all heavy resources respect active chapter scope by default

### Needed Features
- chapter selector after login for multi-chapter users
- persistent active chapter banner/switcher
- chapter-aware dashboards
- self-only athlete/guide pages
- scoped global search

### Needed Metrics / Lenses
- my assigned chapters
- today’s sessions in active chapter
- check-in queue for active chapter
- users with expiring chapter access
- privileged users by chapter

## 2. System/Core

### Priority
P1

### Modules
- notifications
- system modules
- system settings
- audit logs
- media files
- statuses
- categories
- tags
- tag relations

### Validation Steps
1. Compare each model’s fillable/casts/relations to its migration
2. Verify all module/category/status references resolve to real models
3. Normalize soft-delete support across tables that already have `deleted_at`
4. Confirm audit logging is a real behavior, not only seeded data
5. Confirm settings are either runtime settings or explicitly admin metadata
6. Validate taggable polymorphism end to end

### Likely Improvements
- real audit event pipeline
- structured settings service with cached reads
- module-health/admin diagnostics page
- status/category validation helpers

### Needed Metrics / Lenses
- misaligned module declarations
- orphaned category/status references
- recent audit activity by module
- settings changed in last 30 days

## 3. Geography

### Priority
P0

### Modules
- countries
- regions
- chapters
- chapter contacts
- system locations
- location access

### Validation Steps
1. Verify all reverse relationships exist and use correct keys
2. Fix schema/model mismatches on location fields
3. Review the `regions` abstraction
4. Validate timezone, coordinates, and chapter/location linkage
5. Review location access modeling from both user and location sides

### Likely Improvements
- split administrative subdivisions from broad geographic regions
- city/state/country coordinate overrides for real chapters
- chapter-location health checks
- map-friendly location metadata

### Needed Metrics / Lenses
- chapters without active locations
- locations with missing coordinates/timezones
- users with access to inactive locations
- chapters by country / region

## 4. Users and Chapter Roles

### Priority
P1

### Modules
- users
- location access
- support teams
- volunteer-facing relationships

### Validation Steps
1. Validate all user booleans and role assumptions against intended product behavior
2. Audit user-related Nova surfaces for oversharing
3. Replace boolean-only permission logic with scoped chapter-role records
4. Ensure large user indexes are filtered, searchable, and properly indexed
5. Confirm seeded user volume does not break key admin flows

### Likely Improvements
- chapter-role assignment model
- self-service profile surface
- scoped people search
- chapter-aware volunteer roster views

### Needed Metrics / Lenses
- active users by chapter and role
- unverified users
- users with missing required profile data
- guides active in multiple chapters
- athletes without matched guide history

## 5. Certifications and Languages

### Priority
P2

### Modules
- certifications
- certification types
- certification documents
- user certifications
- languages
- language proficiency

### Validation Steps
1. Validate document relationships and file columns
2. Add country applicability relationships where missing
3. Confirm seeded certification types match seeded certifications
4. Remove or fix dead relationships such as non-existent fields
5. Validate expiration tracking and admin workflows

### Likely Improvements
- certification compliance dashboard
- language coverage by chapter
- expiring credential queue
- upload/document verification flows

### Needed Metrics / Lenses
- expiring certifications this month
- users missing required certifications
- chapter language coverage
- guides with multilingual capability

## 6. Workouts and Attendance

### Priority
P0

### Modules
- workouts
- workout sessions
- workout signups
- workout-specific details
- meeting points
- workout session meeting points
- workout equipment assignments
- tandem bike pairings / checks

### Validation Steps
1. Audit all status usage in:
   - controllers
   - Nova actions
   - listeners
   - metrics
2. Confirm session, signup, and attendance workflows are always session-scoped
3. Validate pairing and athlete/guide assignment logic
4. Confirm meeting-point and assignment relationships use correct pivots
5. Check all list defaults, filters, and search for large data behavior
6. Review capacity, waitlist, no-show, cancellation, and late check-in rules

### Likely Improvements
- active chapter + active session workflow for check-in staff
- waitlist promotion rules
- attendance exception handling
- pairing history and preferred-guide suggestions
- sport-specific constraints and capacity metrics

### Needed Metrics / Lenses
- sessions today / this week / next week
- under-guided sessions
- over-capacity sessions
- no-show rates by chapter and sport
- check-ins pending within next 3/6/12 hours
- athlete/guide balance by session

## 7. Equipment and Maintenance

### Priority
P2

### Modules
- equipment
- components
- component types
- manufacturers
- conditions
- maintenance priorities
- maintenance requests
- maintenance logs
- storage locations
- checkouts

### Validation Steps
1. Validate equipment ownership and assignment model
2. Review component compatibility relationships
3. Ensure maintenance request statuses are module-scoped
4. Verify check-in/check-out lifecycle and condition tracking
5. Validate storage, maintenance, and equipment availability queries

### Likely Improvements
- serviceability dashboard
- chapter equipment readiness view
- maintenance SLA tracking
- assignment history and lifecycle reporting

### Needed Metrics / Lenses
- equipment checked out now
- overdue checkouts
- open maintenance by priority
- equipment unavailable by chapter
- components due for service

## 8. Weather

### Priority
P1

### Modules
- weather forecast data
- weather daily data
- weather preferences
- public weather view

### Validation Steps
1. Verify live fetch command works for NYC and then all valid active locations
2. Confirm coordinates and timezones are realistic for seeded chapters
3. Ensure forecast writes are idempotent
4. Confirm weather page/API/UI read the correct fresh data
5. Validate whether weather should store raw WMO codes or mapped local statuses

### Likely Improvements
- active location weather freshness dashboard
- severe weather flags for upcoming sessions
- chapter weather advisory summary
- stale forecast detection

### Needed Metrics / Lenses
- last successful weather pull
- locations with stale forecasts
- sessions in weather risk window
- NYC weather summary for next 48 hours

## 9. Events

### Priority
P2

### Modules
- events
- event registrations
- event equipment
- event travel
- event documents
- event payments
- event results
- event teams
- event accommodations

### Validation Steps
1. Confirm which event modules are actually implemented versus only declared
2. Validate status scoping on events
3. Add missing event categories/types if events are intended to be real operations
4. Review travel/equipment/payment/result dependencies

### Likely Improvements
- event lifecycle workflows
- registration roster views
- staffing and equipment readiness for events

### Needed Metrics / Lenses
- upcoming events by chapter
- registrations open / waitlisted
- event staffing gaps
- event equipment readiness

## 10. Notifications

### Priority
P2

### Modules
- notifications
- communications

### Validation Steps
1. Confirm system notifications are fully aligned with Laravel notification flows
2. Confirm read/unread behavior across Nova and user flows
3. Define whether notifications are operational, user-facing, or both
4. Review future integration points with attendance, maintenance, weather, and events

### Likely Improvements
- notification preferences
- chapter broadcast tools
- weather alert notifications
- attendance reminder notifications

### Needed Metrics / Lenses
- unread notifications by type
- failed notification deliveries
- recent operational alerts

## 11. Reporting, Dashboards, and Analytics

### Priority
P1

### Why
The system needs strong operational visibility to be useful at scale.

### Validation Steps
1. Review all existing metrics and ensure they respect chapter scope
2. Add missing role-based dashboards
3. Identify heavy cards and make sure they aggregate efficiently
4. Define export/report requirements for chapters, regions, and global admins

### Needed Dashboards
- athlete dashboard
- guide dashboard
- check-in staff dashboard
- chapter admin dashboard
- regional admin dashboard
- sys admin dashboard

### Needed Reports
- attendance by chapter/sport/date range
- volunteer coverage by chapter
- no-show and reliability trends
- equipment utilization and maintenance
- certification compliance
- language coverage
- weather impact on sessions

## 12. Declarative / Not Yet Real Modules

### Priority
P3

### Examples
- achievements
- awards
- accessibility requirements
- medical alerts
- vehicles
- sponsorships
- programs
- scholarships
- financial aid
- content moderation
- analytics
- many event-adjacent modules

### Validation Steps
1. Determine whether each declared module is:
   - genuinely planned soon
   - placeholder only
   - obsolete
2. Remove or hide dead declarations from operational admin surfaces
3. Avoid seeding categories/statuses for modules that are not going to be implemented soon

### Likely Improvements
- move future modules to a roadmap doc
- keep `system_modules` aligned with only active or near-term implementation

## Cross-Cutting Backlog

These should be reviewed while moving through the modules above.

### Data Integrity
- align all model fields with migrations
- clean up phantom model references
- ensure module-scoped statuses/categories everywhere

### Performance
- no global unbounded lists for large tables
- default filters on sessions, signups, and users
- chapter scope applied early in queries
- indexes added for active query patterns

### Localization
- all new copy through `lang/*.json`
- role, sport, attendance, and chapter terminology normalized across languages

### Accessibility
- Nova-heavy admin flows reviewed for keyboard and screen-reader viability
- custom user-facing flows favored where Nova cannot be made good enough

### Security and Privacy
- chapter-scoped row visibility
- self-only personal data access
- audit coverage for sensitive changes

## Recommended First Implementation Sequence

1. Auth, chapter roles, active chapter context
2. Workout/session/signup attendance hardening under chapter scope
3. Geography correctness and chapter/location normalization
4. Core system integrity cleanup
5. User domain restructuring around chapter roles
6. Weather live operations and severe-weather workflow
7. Reporting / dashboards / exports
8. Equipment and maintenance
9. Certifications and languages
10. Events
11. Notifications
12. Declarative module cleanup

## Working Rule

Do not attempt to "finish everything" in one pass. Work module domain by module domain, and for each:

1. fix correctness and integrity first
2. scope and secure it second
3. improve operations and visibility third
4. expand feature depth fourth
