# Module Implementation Order

## Phase 0: Canonicalize The Module Catalog
1. Add the six live implemented rows missing from `SystemModuleSeeder`.
2. Rename or retire stale alias rows instead of building duplicate modules:
   - `Workout Locations`
   - `Meeting Points` (`WorkoutMeetingPoint`)
   - `Weather Records` (`WorkoutWeather` / `WeatherLocation`)
   - `Equipment Maintenance`
3. Decide whether the module table is authoritative product scope or just a reference catalog.
4. Treat `Events`, `Tandem Bike Pairings`, and `Tandem Bike Pairing Checks` as partial shells until their deeper workflows are implemented.

## Phase 1: Safety, Compliance, And Access Foundation
Build first because these unlock safe operations and role-based gating.
1. Emergency Contacts
2. Accessibility Requirements
3. Qualification Verification
4. Waivers
5. Safety Incident Severity
6. Safety Incidents
7. Medical Conditions
8. Medical Alerts

## Phase 2: Chapter-Scoped People Operations
Build next because they support staffing, support needs, and operational planning.
1. Assistance Requirements
2. Care Partners
3. Guide Training
4. Guide Experience
5. Volunteer Programs
6. Volunteer Hours
7. Route Difficulty
8. Activity Risk

## Phase 3: Program And Facility Structure
Build before broadening events, because a global multi-sport system needs program and venue structure.
1. Programs
2. Program Sessions
3. Facilities
4. Environmental Conditions
5. Facility Maintenance
6. Transportation
7. Resource Allocation

## Phase 4: Event Platform
Build in dependency order from registration outward.
1. Event Registrations
2. Event Travel
3. Event Accommodations
4. Event Equipment
5. Event Documents
6. Event Teams
7. Event Results
8. Event Payments

## Phase 5: Equipment And Advanced Operations
1. Equipment Fitting
2. Equipment Customization
3. Equipment Loan
4. Vehicle Management
5. Vehicle Maintenance
6. Insurance Coverage

## Phase 6: Finance, Recognition, And Engagement
1. Financial Aid
2. Scholarships
3. Expense Tracking
4. Achievements
5. Awards
6. Volunteer Recognition
7. Award Ceremonies
8. Sponsorships

## Phase 7: Communications, Reporting, And Governance
1. Communications
2. Reports
3. Analytics
4. Document Templates
5. Content Moderation
6. Performance Evaluations
7. Workout Feedback

## Lower-Priority Optional Modules
- Dietary Preferences
- Medications
- Support Teams

## Why This Order
- It establishes safe, scoped operations before adding volume-heavy feature surfaces.
- It favors modules that unblock many other modules instead of isolated CRUD.
- It aligns with an adaptive sports organization that must manage people, chapters, and risk before it optimizes recognition or finance flows.
