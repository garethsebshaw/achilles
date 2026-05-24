# Vuexy Frontend Worklog

## 2026-05-24

### Read / audit completed

- Reviewed `CODEX-START-HERE.md`
- Reviewed `AGENTS.md`
- Audited current Laravel/Nova routing and frontend setup
- Audited local Vuexy package variants
- Confirmed current app already has:
  - Vue 3
  - Vite
  - ApexCharts

### Architecture conclusion

- Do not replace Nova.
- Do not skin Nova into Vuexy.
- Build a separate frontend surface.
- First slice should be a read-only dashboard with real data.
- URL should avoid `vu`, `vuexy`, and `nova`; use a useful functional path instead.
- Accessibility should be designed into the frontend prototype immediately.

### Next implementation steps

- [x] Add portal dashboard controller
- [x] Add portal routes
- [x] Add aggregated metrics endpoint(s)
- [x] Add portal blade mount page
- [x] Add portal Vue app entry
- [x] Add real signup growth charts
- [x] Add accessibility preferences and chart table fallback
- [x] Validate locally
- [x] Fix Blade config bootstrap parsing
- [x] Fix Vite manifest output for browser rendering
- [x] Rebuild the dashboard into a Vuexy-style analytics layout
- [x] Add chapter activity table
- [x] Normalize seeded historical series across a one-year window
- [x] Turn auto refresh off by default and make it user-controlled

### Validation completed

- `php artisan route:list | rg 'portal/dashboard'`
- `php artisan test tests/Feature/PortalDashboardTest.php tests/Feature/Nova/NovaSmokeTest.php tests/Feature/Nova/NovaCrudActionTest.php`
- `php artisan test tests/Feature/PortalDashboardTest.php`
- `npm run build`
- local browser validation of `http://achillesworkouts.test/portal/dashboard`

### Phase 2 started

- [x] Add runtime bridge config
- [x] Add runtime bridge request signer
- [x] Add runtime bridge signature middleware
- [x] Add read-only runtime endpoints:
  - [x] health
  - [x] logs
  - [x] queue summary
- [x] Add runtime bridge feature tests
- [x] Validate runtime routes and full test suite

### Phase 3 started

- [x] Add `config/demo_activity.php`
- [x] Add user registration redistribution service
- [x] Add daily new user generation service path
- [x] Add workout signup redistribution service
- [x] Add historic workout signup backfill path
- [x] Add rolling future workout signup maintenance path
- [x] Add `demo:simulate-activity` console command
- [x] Add scheduler hooks for demo activity
- [x] Add authenticated admin endpoints for remote demo-activity execution and summary verification
- [x] Fix portal shell regressions after Vuexy-style shell integration
- [x] Validate local redistribution and signup creation behavior
- [x] Validate full test suite after demo activity changes
- [ ] Deploy and run remote redistribution/maintenance pass

### Future queued work

- [ ] Codex bridge
- [ ] Chapter completeness reconciliation
- [ ] Frontend auth/portal expansion
