# Module Coverage Audit

## Summary
- Live `system_modules` rows: `103`
- Fully implemented modules with a real model file and a material workflow behind them: `40`
- Partially implemented / thin-shell modules: `3`
- Declared rows that should be mapped to an existing implemented module instead of being built separately: `5`
- Genuinely missing declared modules with no model file today: `55`
- Extra implemented module rows present in the database but missing from the canonical `SystemModuleSeeder`: `6`

## Fully Implemented
- `System Modules`
- `System Settings`
- `System Audit Logs`
- `Media Files`
- `Tags`
- `Tag Relations`
- `Statuses`
- `Countries`
- `Regions`
- `Chapters`
- `Chapter Contacts`
- `Users`
- `Language Proficiency`
- `Languages`
- `Certifications`
- `Certification Types`
- `Certification Documents`
- `Equipment Management`
- `Equipment Components`
- `Component Types`
- `Equipment Manufacturers`
- `Equipment Conditions`
- `Equipment Maintenance Priorities`
- `Maintenance Requests`
- `Maintenance Logs`
- `Storage Locations`
- `Equipment Checkouts`
- `System Locations`
- `Location Access`
- `Workouts`
- `Workout Sessions`
- `Workout Specific Details`
- `Workout Signups`
- `Workout Equipment Assignments`
- `Notifications`
- `Meeting Points`
- `Workout Session Meeting Points`
- `Weather Preferences`
- `Weather Forecast Data`
- `Weather Daily Data`


## Partially Implemented / Thin Shells
- `Events`: loads as a Nova resource shell, but the broader event platform is still missing.
- `Tandem Bike Pairings`: model exists, but the pairing workflow is still skeletal and not surfaced as a complete admin module.
- `Tandem Bike Pairing Checks`: validation-result model exists, but there is no complete admin workflow around pairing checks yet.

## Declared But Should Be Mapped, Not Built Separately
- `Equipment Maintenance`: Keep as a catalog alias only. Real maintenance behavior already lives in Maintenance Requests and Maintenance Logs.
- `Workout Locations`: Do not build a separate WorkoutLocation model. Route workout geography through SystemLocation and meeting-point relations.
- `Meeting Points`: Legacy WorkoutMeetingPoint row is stale. The real implemented module is App\Models\MeetingPoint.
- `Weather Records (WorkoutWeather)`: Do not build WorkoutWeather. Use WeatherData, WeatherDailyData, and WeatherPreference tied to SystemLocation.
- `Weather Records (WeatherLocation)`: Do not build a WeatherLocation model. Weather location scope is already SystemLocation.

## Genuinely Missing And Needing Specs
- `Emergency Contacts`
- `Dietary Preferences`
- `Accessibility Requirements`
- `Guide Training`
- `Guide Experience`
- `Equipment Fitting`
- `Equipment Customization`
- `Equipment Loan`
- `Workout Feedback`
- `Route Difficulty`
- `Event Registrations`
- `Event Travel`
- `Event Equipment`
- `Event Teams`
- `Event Documents`
- `Event Results`
- `Event Payments`
- `Event Accommodations`
- `Volunteer Programs`
- `Volunteer Hours`
- `Volunteer Recognition`
- `Safety Incidents`
- `Safety Incident Severity`
- `Activity Risk`
- `Insurance Coverage`
- `Transportation`
- `Vehicles`
- `Vehicle Maintenance`
- `Awards`
- `Achievements`
- `Award Ceremonies`
- `Communications`
- `Reports`
- `Analytics`
- `Performance Evaluations`
- `Sponsorships`
- `Content Moderation`
- `Resource Allocation`
- `Qualification Verification`
- `Medical Conditions`
- `Medical Alerts`
- `Medications`
- `Waivers`
- `Document Templates`
- `Financial Aid`
- `Scholarships`
- `Expense Tracking`
- `Programs`
- `Program Sessions`
- `Assistance Requirements`
- `Care Partners`
- `Support Teams`
- `Facilities`
- `Facility Maintenance`
- `Environmental Conditions`

## Extra Live Rows Missing From `SystemModuleSeeder`
- Row `98`: `Meeting Points` -> `App\Models\MeetingPoint`
- Row `99`: `Workout Session Meeting Points` -> `App\Models\WorkoutSessionMeetingPoint`
- Row `100`: `Weather Preferences` -> `App\Models\WeatherPreference`
- Row `101`: `Weather Forecast Data` -> `App\Models\WeatherData`
- Row `102`: `Weather Daily Data` -> `App\Models\WeatherDailyData`
- Row `103`: `Tandem Bike Pairing Checks` -> `App\Models\TandemBikePairingCheck`

## Key Conclusions
- The module table overstates what exists in code today.
- Some rows are stale aliases and should be reconciled rather than implemented as new modules.
- The missing set is large enough that implementation order matters more than module count.
- The highest-leverage foundation work remains authorization, chapter scope, safety/compliance, and event/program abstractions.
