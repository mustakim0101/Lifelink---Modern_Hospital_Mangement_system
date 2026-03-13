# Future Notes

## Backlog Ideas
- need to make backup for database to get data from previous use for fatabase
- [Add backlog item]
- [Add backlog item]

## Future Improvements
- Do not delete `app/Models`. Those are not the problem. Models are part of normal Laravel app code even in a database-first project.

The real conflict is `database/migrations`.

Best options, from most practical to least:

**Option 1: Keep migrations, but declare them as secondary**
This is the safest choice for your current project.

Do this:
- keep [lifelink-app/database/migrations](/s:/Lifelink---Modern_Hospital_Mangement_system/lifelink-app/database/migrations)
- keep the SQL files in [docker/mssql/init/schema](/s:/Lifelink---Modern_Hospital_Mangement_system/docker/mssql/init/schema) as the official source of truth
- document clearly in README/docs:
  - Docker SQL init creates the actual database
  - Laravel migrations are retained for reference/history only
  - normal setup should not depend on `php artisan migrate`

This avoids commit mess and keeps your repo stable.

**Option 2: Move old migrations out of the active migrations path**
If you want less confusion, move them to something like:
- `lifelink-app/database/migrations_archive/`

Then Laravel will stop seeing them as runnable migrations unless you explicitly point to that path.

This is cleaner than deleting, but only do it if you are sure nothing in your workflow still expects them.

**Option 3: Keep only one minimal placeholder migration set**
Not ideal for you now. Too much churn for little gain.

**What not to do**
- do not delete `app/Models`
- do not keep running `php artisan migrate` as part of your normal setup if SQL files are your real DB source
- do not try to maintain both SQL schema and migrations as equally authoritative unless you want double maintenance forever

**My recommendation for your project**
Use this rule:

- `Source of truth for schema`: raw SQL in `docker/mssql/init/schema`
- `Source of truth for startup`: `docker compose up`
- `Models`: active application layer, keep them
- `Migrations`: retained for reference/history, not primary setup path

Then add a clear note in docs such as:
“Although Laravel migration files are present in the repository, this project initializes its MSSQL schema through Docker-mounted SQL scripts. The migration folder is retained for reference and earlier development history.”

- [Add improvement]
- [Add improvement]

## Scaling Ideas
- [Add scaling idea]
- [Add scaling idea]

## Security and Reliability Notes
- [Add note]
- [Add note]

## Documentation To Add Later
- [Add future doc item]
- [Add future doc item]

## Open Questions
- [Add question]
- [Add question]

## Later Reference Notes
[Add future reference notes here]

---

## Revision 2 - Consolidated Future Planning Notes

This section is the cleaner working version of future planning. Existing notes above are preserved, including the current backup reminder.

## Main Backlog Direction
The project already has a strong feature foundation. Future work should now focus on turning the current build into a cleaner, safer, more maintainable product rather than only adding new screens.

## High-Priority Future Improvements
- improve the UI from prototype pages into a unified product experience
- complete comprehensive testing for major role-based workflows
- prepare API documentation for all implemented endpoints
- improve deployment and environment setup for easier reuse on new machines
- reduce duplicated or split documentation across files when the structure becomes stable

## Data Safety and Backup
Existing important note:
- need to make backup for database to get data from previous use for database

Expanded direction:
- define a repeatable MSSQL backup and restore process
- document how to preserve seeded/demo/test data
- decide how Docker volumes should be handled in development versus presentation resets
- prepare a safe restore process before destructive environment resets

## Future UI Direction
- shared layout for all pages
- role-based dashboard landing pages
- cleaner navigation and routing
- responsive design pass
- separation of user-facing screens from admin/IT diagnostic tools

## Future Backend Direction
- add more formal service-layer separation where controller logic is currently too large
- standardize business logic placement between controllers, models, and services
- review whether the project should remain hybrid or move more clearly toward database-first or code-first
- add stronger validation and error messaging consistency across all modules

## Testing Direction
- feature tests for auth and role access
- tests for job application lifecycle
- tests for admission and bed assignment flow
- tests for donor enrollment, availability, and donation
- tests for blood request matching and notification response flow
- regression tests for protected routes and account freeze behavior

## Scalability Ideas
- support larger data volumes in admissions, donor records, and request history
- add pagination and filtering to all large list views
- prepare reporting summaries that can scale beyond demo-size datasets
- think about audit logs and performance monitoring for important workflows

## Security and Reliability Ideas
- formalize role-based access review for all sensitive endpoints
- improve logging around critical actions such as freeze/unfreeze, donor notifications, and bed assignment
- review backup and restore strategy before production-like usage
- ensure safer handling of environment secrets and credentials

## Documentation To Add Later
- deployment guide for a fresh machine
- environment setup guide for teammates
- role-based user manual
- testing checklist for final submission/demo
- database workflow explanation for SQL-first or hybrid operation

## Open Questions to Resolve Later
- should the project stay hybrid or choose one schema source of truth
- should advanced diagnostics remain inside role dashboards or move to separate pages
- should the frontend remain Blade-based or move to Livewire/Inertia later
- how much demo data should be automatically seeded

## Long-Term Expansion Ideas
- hospital reporting dashboard
- more detailed medical record workflows
- improved patient history timeline
- branch or multi-department expansion features
- stronger notification channels
