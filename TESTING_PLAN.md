# Automated Testing Plan

## 1. Why this plan exists

The core maintainer of this application has left the company.
Dependency updates (Dependabot/Renovate PRs) currently cannot be verified beyond PHPStan, PHPCS, Doctrine schema validation, and 11 unit test methods covering ~1% of 961 backend source files.
The frontend (270 TypeScript files) has zero tests and no test runner installed.

The goal of this plan is **confidence in dependency upgrades and small changes without deep domain knowledge**.
That goal drives the prioritization: we test the seams that dependencies touch (HTTP, ORM, RDF serialization, UI rendering) before we chase coverage of business logic.

Two constraints shape the execution order:

- **Local first.** The project still has a CircleCI config, but CircleCI is no longer used and CI will move to GitHub Actions.
  Every suite in this plan must therefore run locally with a single command against the Docker Compose stack; CI (whenever it lands) is a thin wrapper around those same commands.
- **Stardog is dead code.** The Stardog triplestore was a POC and is no longer used; it gets no test coverage, and Stardog-related code is marked deprecated instead (see §4.7).

## 2. Current state

| Area                      | State                                                                           |
|---------------------------|---------------------------------------------------------------------------------|
| Backend unit tests        | 7 test files, 11 test methods, all in `tests/unit/`                             |
| Backend integration tests | None (no `WebTestCase`, no test database, no fixtures)                          |
| Frontend tests            | None (no Jest/Vitest/Cypress/Playwright installed)                              |
| E2E tests                 | None                                                                            |
| CI                        | CircleCI config exists (`phpunit`, `phpstan` level 7, `phpcs`, `doctrine:schema:validate --skip-sync`) but CircleCI is no longer used; GitHub Actions migration pending |
| CI database               | None (schema validation runs with `--skip-sync`, so no live DB is exercised)    |

Existing assets we build on:

- PHPUnit 10.5 with strict mode already configured (`phpunit.xml.dist`).
- `.env.test` and `config/packages/test/` already exist.
- PHPStan level 7 with strict rules — a strong type-level safety net already in CI.
- Docker Compose stack (MySQL 8.0, PHP 8.2-fpm, nginx) that an E2E suite can target.

External dependencies that constrain test design:

- **Castor EDC API** — accessed via `src/Model/Castor/ApiClient.php` (Guzzle 6). The Guzzle client is instantiated inside the constructor (`new Client()` at `src/Model/Castor/ApiClient.php:61`), which blocks mocking and must be refactored to constructor injection.
- **Stardog triplestore** — accessed via `src/Graph/SparqlClient.php` (EasyRdf). **Deprecated**: Stardog was a POC and is no longer in use; code paths that reach it get stubbed in tests, never covered.
- **ORCID OAuth, BioPortal, Slack webhooks** — peripheral, mocked or disabled in tests.

## 3. Target test architecture

A standard pyramid, with one project-specific addition: **RDF snapshot tests**, because RDF serialization is this application's core output and is directly exercised by `easyrdf`, `arc2`, and `json-ld` upgrades.

Every layer runs locally first; the "Cadence" column describes how often it should run once GitHub Actions exists, but nothing in this plan depends on CI being in place.

| Layer                          | Tooling                                                                             | Local command (target)       | Cadence                            |
|--------------------------------|-------------------------------------------------------------------------------------|------------------------------|------------------------------------|
| Backend unit                   | PHPUnit 10.5 (existing)                                                             | `composer test:unit`         | Every change                       |
| Backend integration (API + DB) | `symfony/test-pack` (WebTestCase), `dama/doctrine-test-bundle`, `zenstruck/foundry` | `composer test:integration`  | Every change                       |
| RDF snapshot                   | PHPUnit golden-file comparisons of Turtle/JSON-LD output                            | part of unit/integration     | Every change                       |
| Frontend unit + component      | Jest + `@testing-library/react` + `@testing-library/user-event` + MSW               | `yarn test`                  | Every change                       |
| Frontend type check            | `tsc --noEmit`                                                                      | `yarn typecheck`             | Every change                       |
| E2E                            | Playwright against the Docker Compose stack                                         | `yarn test:e2e`              | Nightly + risky dependency updates |

The integration suite uses the MySQL service from `docker-compose.yml`; the backend commands run inside the `symfony` container.

Tooling rationale:

- **Jest over Vitest**: the build is Webpack Encore (not Vite), Babel with the React preset is already configured (so `babel-jest` works without adding `ts-jest`), and `@types/jest` is already a dependency.
- **Foundry over hand-rolled fixtures**: 174 entities with deep relationships make per-test factories far more maintainable than global fixture sets.
- **DAMA doctrine-test-bundle**: wraps every test in a rolled-back transaction, so the test database is migrated once per CI run and stays clean.
- **Playwright over Cypress**: better multi-page/content-negotiation support, which matters for testing RDF endpoints, and no paid dashboard needed.

## 4. Phase 0 — Foundations (prerequisite for everything else)

**Outcome: every developer can run unit and database-backed integration tests locally with one command, using the Docker Compose stack.**

1. Add dev dependencies: `symfony/test-pack`, `dama/doctrine-test-bundle`, `zenstruck/foundry`, `symfony/http-client` (test utilities only if needed).
2. Split `phpunit.xml.dist` into two suites: `unit` (`tests/unit`, no kernel, no DB) and `integration` (`tests/integration`, boots kernel, uses DB).
3. **Local test database via docker-compose**: add a test database (e.g. `fdp_test`) on the existing MySQL 8.0 service (or a dedicated `mysql-test` service), created by the init container, with `bin/console doctrine:migrations:migrate -n --env=test` to set it up.
   Tests run inside the `symfony` container so PHP version and extensions match production.
4. **One-command entry points**: composer scripts `test:unit` and `test:integration` (and a `yarn test-php`-style wrapper that executes them in the container), so "how do I run the tests" has exactly one answer.
5. Configure DAMA transaction rollback as a PHPUnit extension for the integration suite, so the test database is migrated once and stays clean.
6. Define a test `DATABASE_URL` in `.env.test` and make `config/packages/test/doctrine.yaml` work against the local container (drop the production SSL CA requirement for the test env).
7. **Mark Stardog code as deprecated**: add `@deprecated` annotations to the Stardog/SPARQL POC code (`src/Graph/SparqlClient.php`, `FederatedSparqlResponse`, `FederatedQueryResult`, `src/Controller/FAIRData/Data/FederatedSPARQLController.php`, and the `STARDOG_*` env vars in `.env.dist`).
   These files get no test coverage; anything that touches `SparqlClient` in a test gets a stub.
   Actual removal is a separate cleanup task outside this plan.
8. **Refactor `ApiClient` to accept a Guzzle `ClientInterface` via the constructor** so tests can inject a `MockHandler`. This is the single most important refactor in the plan; without it the 694-line Castor integration layer is untestable.
9. Add coverage tooling: the symfony image already ships Xdebug (disabled by default), so `composer test:coverage` loads it with `xdebug.mode=coverage` for an HTML + text report in `var/coverage`. Switch to `pcov` later if coverage runs become too slow. No threshold yet — measure first.
10. Create Foundry factories for the core entity graph: `User`, `Person`, `CastorUser`, `Catalog`, `Dataset`, `Distribution`, `Study`/`CastorStudy`, the four `*Permission` entities, and `MetadataModelVersion`.

Estimated effort: 3–5 days.

### Phase 0 status: completed (June 2026)

All items above are merged on `add-test-harness`.
Two pre-existing bugs were found and fixed along the way: a 2024 migration that failed on every fresh database (stray columns from the generating DB), and PHPUnit booting the dev kernel because the symfony container exports `APP_ENV=dev` into `$_ENV`.

### Tracked leftovers from Phase 0

- [ ] **Schema drift cleanup migration**: the database still has `created_by`/`updated_by` audit columns (plus their FKs/indexes) that the entities no longer map — all nullable, so harmless for tests.
  Generate a cleanup migration so `doctrine:schema:validate` can run *without* `--skip-sync` (see §6.4).
- [ ] **Security advisory backlog**: `composer audit` reports 34 advisories across 12 packages.
  Work through these once the harness gives enough coverage to upgrade safely; the Guzzle 6 → 7 major is the riskiest and now has `ApiClient` test coverage.
- [ ] **Guzzle 6 deprecation noise**: the unit suite reports one PHP deprecation from Guzzle 6's `MockHandler` on PHP 8.2; it disappears with the Guzzle 7 upgrade.
- [ ] **`MetadataModelVersionFactory`**: deferred because it requires the whole DataSpecification aggregate; add it together with the metadata tests (§5 item 2 / §6 item 2).
- [ ] **PHPStan baseline contains 6 intentional entries** for live classes that still inject the deprecated `TripleStoreBasedDistributionService`; remove them together with the Stardog code removal.
- [ ] **Stardog code removal**: everything is marked `@deprecated` (§4.7); actual deletion (code, `STARDOG_*` env vars, services.yaml wiring, `RDFDistributionDatabaseType::STARDOG`) is a separate cleanup task.
- [ ] **Broken `php-xdebug` wrapper**: `docker/symfony/php-xdebug` sets Xdebug config but never loads the extension (its ini was renamed to `.off` at build time), so it silently runs without Xdebug.
  Not needed for coverage (`composer test:coverage` loads the extension explicitly), but fix or remove it to avoid confusion.
- [ ] **Port 3306 collisions**: developers who also run the edc stack locally need a `docker-compose.override.yml` remapping this project's MySQL host port; consider committing a documented `docker-compose.override.yml.dist`.

## 5. Phase 1 — Backend unit tests

**Outcome: the pure-logic core of the application is covered without needing a database.**

Priority order, chosen by (risk × dependency-upgrade exposure ÷ effort):

1. **Authorization voters** (`src/Security/Authorization/Voter/`, 7 classes).
   Pure logic, security-critical, and broken voters fail silently.
   Test matrix per voter: anonymous/user/admin × published/unpublished × view/edit/manage × with/without permission entity.
2. **RDF rendering helpers** (`src/Service/RDF/RenderRdfMetadataHelper.php`, `RenderRdfDataHelper.php`).
   Use golden-file snapshot tests: build an entity graph in memory, render to Turtle/JSON-LD, compare against a committed fixture file.
   These tests are the primary guard for `easyrdf`, `arc2`, and `json-ld` upgrades.
3. **API request objects** (`src/Api/Request/`).
   Each request class encodes validation constraints and parsing; test valid payloads, each constraint violation, and edge cases (null/missing/wrong type).
   Two examples already exist under `tests/unit/Api/` — extend the pattern.
4. **Castor `ApiClient`** (after the Phase 0 refactor).
   Use Guzzle `MockHandler` with recorded JSON responses for each endpoint method: success, 401 token refresh, 404, malformed body.
   This is the primary guard for the eventual Guzzle 6 → 7 upgrade, which is one of the riskiest pending updates.
5. **Custom Doctrine types** (`src/Type/`, 22 classes) and **validators** (`src/Validator/`, 10 classes).
   Small, mechanical, high value for `doctrine/dbal` upgrades: `convertToDatabaseValue`/`convertToPHPValue` round-trips for every enum value.
6. **Entity behavior**: `User::getRoles()` (role derivation including the castoredc.com admin rule), `Dataset/Catalog/Distribution::getPermissionsForUser()`, encryption behavior on `CastorServer` (partially covered today), and `MetadataFieldType` (336 lines of field-type logic).
7. **Command handlers** (`src/CommandHandler/`, 141 classes) with mocked `EntityManager`/bus — start with the complex ones: `UpdateMetadataCommandHandler`, distribution generation handlers, permission handlers.
   Do not aim for all 141; cover the top ~20 by complexity and let integration tests (Phase 2) cover the rest end-to-end.

Estimated effort: 2–3 weeks for the first six categories; command handlers are ongoing.

### Phase 1 status (June 2026)

Items 1–6 are largely done: 536 new unit tests across voters (all 7, full decision matrix), RDF snapshot tests (18 golden fixtures for the GraphResources and both render helpers, refresh via `UPDATE_RDF_SNAPSHOTS=1`), 11 API request classes, all 21 custom Doctrine types (discovery-driven, self-guarding), all 10 validators, and entity behavior (`User::getRoles()`, `getPermissionsForUser()`, `MetadataFieldType`, `Iri`/`Version`).
Still open in this phase:

- [ ] Command handler unit tests (§5.7, top ~20 by complexity) — not started.
- [ ] RDF snapshot coverage for annotated-value paths (option groups, country/license/language/ontology pickers) and repeated groups — deferred until the metadata-domain entity graph helpers exist.
- [ ] `MetadataValuesApiRequest` — constraints are built dynamically from `MetadataModelVersion`; needs the metadata factories.

### Findings from Phase 1 tests (tracked for review; tests pin current behavior)

Authorization (needs a security review):

- [ ] `Catalog::isPublic()` is hardcoded `true`, so every catalog is publicly viewable and the unpublished-catalog branch in `CatalogVoter` is unreachable.
- [ ] `CatalogVoter` denies add/edit/manage to non-Castor users even with a manage permission; `DatasetVoter`/`DistributionVoter` have no such gate — inconsistent.
- [ ] `DataSpecificationVoter`'s `add` attribute is only ever grantable to `ROLE_ADMIN`, never via permissions (likely an oversight).
- [ ] `DistributionVoter` denies `access_data` even to admins when contents are null (short-circuit precedes the admin check).
- [ ] `DistributionContentsVoter` ignores the contents' own permission entities and can null-dereference when a dataset has no study.
- [ ] `StudyVoter`'s admin bypass covers `edit` but not `edit_source_system`.

API robustness (clients get 500s where 400s are intended):

- [ ] Non-nullable typed request properties throw `TypeError` in `parse()` on missing/null/empty/wrong-type input before validation runs, making many `NotBlank`/`Type` constraints dead code.
- [ ] `DistributionApiRequest::parse()` never reads `dataDictionary`/`dataDictionaryVersion`, so the `csv` validation group can never pass.
- [ ] `getGroupSequence()` in the distribution requests builds enums from raw strings, so an unknown `type` crashes during validation instead of failing the `Choice` constraint.
- [ ] `boolval()` coercion: `'false'` becomes `true`, and missing flags silently become `false` (bypassing `NotNull`).
- [ ] `MetadataFilterApiRequest::$hideParents` is never assigned in `parse()`.

Smaller code-quality findings:

- [ ] `Version`'s regex uses the `/m` flag, so multi-line strings containing one valid version line are accepted; `new Version(null)` leaves typed properties uninitialized (getters throw `Error`).
- [ ] `MetadataFieldType::DROPDOWN` maps to the `paragraph` display type while checkboxes/radio map to `list`; the `TYPE_PLAIN`/`TYPE_ANNOTATED` constants are constructible via `fromString()` but crash the mapping methods.
- [ ] `SlugValidator` queries the repository even for forbidden slugs and has no null guard; `OptionGroupOptions` declares message properties its validator never uses; `User::EMAILS` is an empty const making its admin-override branch dead code.

## 6. Phase 2 — Backend integration and API tests

**Outcome: every API domain has request-to-database coverage, which is what actually validates Symfony, Doctrine, and Messenger upgrades.**

1. **A base `ApiTestCase`** extending `WebTestCase` with helpers: `loginAs(User $user)` via Symfony's `loginUser()`, JSON request/assert helpers, and Foundry integration.
2. **Per-domain API test classes** mirroring `src/Api/Controller/`: Catalog, Dataset, Distribution, Study, Metadata, DataSpecification, Security/Agents.
   For each domain cover: list, detail, create, update, delete; a 403 for each voter attribute; a 422 for an invalid payload; pagination envelope shape.
   These tests dispatch real commands through the real Messenger bus into real handlers and the real database, so they cover the CQRS layer broadly without 141 individual handler tests.
3. **Public FDP endpoint tests** (`src/Controller/FAIRData/`): the public, unauthenticated FAIR Data Point routes with content negotiation (`Accept: text/turtle`, `application/ld+json`, HTML).
   Assert valid, parseable RDF using EasyRdf itself plus snapshot comparison.
   These are the highest-value tests in the whole plan relative to effort: public contract, no auth, exercises routing + ORM + RDF serialization in one pass.
4. **Migration check**: running the integration suite always migrates from an empty database, which is itself the migration test — make that failure mode explicit, and run `doctrine:schema:validate` *without* `--skip-sync` afterwards.
5. **Repository tests** for the 19 repositories with non-trivial DQL/native queries, since DQL behavior changes across Doctrine upgrades.
6. **SPARQL boundary**: integration tests stub `SparqlClient` and assert nothing about it — the Stardog path is deprecated (§4.7) and intentionally uncovered.

Estimated effort: 3–4 weeks for full domain coverage; the public FDP endpoint tests (item 3) should land first, in days.

## 7. Phase 3 — Frontend tests

**Outcome: utility logic, hooks, and the heavyweight forms are covered; TypeScript itself becomes a quality gate.**

1. **Setup**: add `jest`, `babel-jest`, `@testing-library/react`, `@testing-library/user-event`, `@testing-library/jest-dom`, `msw`, and `jest-environment-jsdom`; add `yarn test` and wire `tsconfig` path aliases into Jest's `moduleNameMapper`.
2. **Add a `yarn typecheck` script (`tsc --noEmit`) immediately.**
   Today nothing type-checks the frontend (Encore builds transpile via Babel without type errors failing the build), so MUI/React/TypeScript upgrades can silently break types.
   This one script is the cheapest possible win for frontend dependency safety.
3. **Unit tests for utils**: `MetadataFormHelper.ts` (Yup schema generation — guards Yup/Formik upgrades), `util.tsx` (localization, debounce, text parsing), `BreadcrumbUtils.ts`, `jsonLdUtils.ts`, `PermissionHelper.ts`.
4. **Hook tests with MSW**: the 9 data-fetching hooks (`useGetDataset`, `useJsonLdRepresentation`, etc.) — success, error notification, loading state.
   MSW also guards the Axios upgrade path (interceptor behavior, 401 redirect in `src/js/network/apiClient.tsx`).
5. **Component tests for the high-risk surfaces**: `DatasetForm`, `DistributionForm`, `MetadataForm` (dynamic rendering from a metadata model — the most upgrade-sensitive component), `PermissionEditor`, and the `QueryBuilder` rule components.
   Render with RTL, drive with `user-event`, mock the network with MSW; assert on accessible roles/labels, not DOM structure, so MUI upgrades only fail tests when behavior actually changes.
6. Defer testing purely presentational components; they are covered more cheaply by E2E smoke tests.

Estimated effort: 2–3 days setup + typecheck job; 2–3 weeks for items 3–5.

## 8. Phase 4 — End-to-end tests (Playwright)

**Outcome: a small smoke suite proving the assembled stack (containers, nginx, PHP-FPM, MySQL, built JS bundle) works.**

1. Run Playwright against the existing `docker-compose` stack (build images, migrate, seed via a fixture command, run tests) — locally first, scheduled in GitHub Actions later.
2. **Authentication strategy**: Castor OAuth cannot run unattended.
   Add a test-environment-only authenticator (a firewall entry enabled solely when `APP_ENV=test`/a dedicated env flag) that logs in a seeded user, mirroring what `loginUser()` does in WebTestCase.
   This must be provably unreachable in production configuration — gate it on env and cover that gate in a unit test.
3. **Smoke journeys**, in priority order:
   - Anonymous: browse FDP home → catalog → dataset → distribution; assert pages render and RDF content negotiation returns parseable Turtle/JSON-LD.
   - Authenticated: log in, dashboard renders, open a study, edit dataset metadata through the dynamic `MetadataForm`, save, verify persistence.
   - Admin: create a catalog and dataset end to end.
4. Keep the suite small (≤ 15 scenarios) and stable; E2E exists to catch "the app does not boot / the bundle is broken / the form cannot submit" classes of failure, not to re-test logic.
5. Cadence: run locally before merging risky dependency updates; once GitHub Actions exists, schedule nightly on `master`.

Estimated effort: 1–2 weeks including the test authenticator.

## 9. CI — GitHub Actions migration (after the suites run locally)

CircleCI is no longer in use, so the existing `.circleci/config.yml` is dead config; it gets replaced by GitHub Actions workflows once the local suites exist.
Because every suite is a one-command wrapper (§3), the migration is thin: each job below just checks out, installs, and runs the same command a developer runs locally.
The CircleCI-specific pieces to carry over are the Composer auth for `satis.castoredc.net` (as an Actions secret) and dependency caching.

| Job                                 | Contents                                                            | Trigger                          |
|-------------------------------------|---------------------------------------------------------------------|----------------------------------|
| `unit-tests`                        | `composer test:unit`                                                | Every PR                         |
| `integration-tests`                 | MySQL 8.0 service container → migrate → `composer test:integration` | Every PR                         |
| `phpstan` / `phpcs` / `orm-mapping` | Same commands as the CircleCI jobs                                  | Every PR                         |
| `frontend-typecheck`                | `yarn typecheck`                                                    | Every PR                         |
| `frontend-tests`                    | `yarn test --ci` with coverage artifact                             | Every PR                         |
| `frontend-build`                    | `yarn build` (catches Encore/Webpack/loader upgrade breakage)       | Every PR                         |
| `e2e`                               | Docker Compose up → Playwright                                      | Nightly + manual/label-triggered |

Delete `.circleci/config.yml` once the Actions workflows are green, so Dependabot PRs are not gated on a CI system that never runs.

## 10. Dependency-update playbook

Once Phases 0–3 are merged (and the GitHub Actions workflows from §9 exist), a green pipeline on a Dependabot/Renovate PR means: types check, static analysis passes, unit + integration + frontend tests pass, migrations run from scratch, and the production JS bundle builds.
Until CI lands, the same guarantee comes from running the §3 commands locally against the PR branch.

Risk map — which test layer guards which dependency class:

| Dependency class                            | Primary guard                                                          |
|---------------------------------------------|------------------------------------------------------------------------|
| `symfony/*`                                 | Integration/API tests, migration job                                   |
| `doctrine/*`                                | Custom type unit tests, repository tests, migration job, `orm-mapping` |
| `guzzlehttp/guzzle` (6 → 7 pending)         | `ApiClient` unit tests with MockHandler                                |
| `sweetrdf/easyrdf`, `semsol/arc2`, `jsonld` | RDF snapshot tests, public FDP endpoint tests                          |
| `react`, `@mui/*`, `formik`, `yup`          | `tsc --noEmit`, component tests, E2E smoke                             |
| `axios`                                     | apiClient/hook tests with MSW                                          |
| `webpack`/Encore/Babel/TypeScript           | `frontend-build` job, typecheck job                                    |
| PHP / Node version bumps                    | Entire pipeline + E2E                                                  |

Process for each dependency PR: green pipeline → merge minor/patch updates without manual review; for majors (or anything in the RDF/auth stack), additionally trigger the E2E job and skim the changelog.

## 11. Coverage targets and ratcheting

Absolute coverage percentages are a poor goal for a 961-file codebase starting near zero; use a ratchet instead:

1. After Phase 1, record the baseline coverage from a local coverage run.
   Measured June 2026 after the first Phase 1 pass (`composer test:coverage`): **8.87% lines** (1361/15343), 13.07% methods, up from ~1% before Phase 0.
2. Once GitHub Actions exists, add a check that fails if line coverage of `src/` drops more than 0.5 points below the recorded baseline; bump the baseline as it grows.
3. Indicative milestones (not gates): ~25% backend lines after Phase 1, ~45% after Phase 2 with the API domains covered, and near-100% for the named critical classes (voters, RDF helpers, `ApiClient`, custom types).

## 12. Sequencing summary

| Order | Work                                                                     | Effort    | Unblocks                           |
|-------|--------------------------------------------------------------------------|-----------|------------------------------------|
| 1     | Phase 0 foundations + `ApiClient` refactor + Stardog deprecation markers | 3–5 days  | Everything                         |
| 2     | Public FDP endpoint tests + RDF snapshots (§6.3, §5.2)                   | ~1 week   | RDF dependency upgrades            |
| 3     | Voters, request objects, types/validators (§5.1, 5.3, 5.5)               | ~1 week   | Security confidence                |
| 4     | `yarn typecheck` + frontend test setup (§7.1–7.2)                        | 2–3 days  | Frontend dependency upgrades       |
| 5     | API domain integration tests (§6.2)                                      | 3–4 weeks | Symfony/Doctrine upgrades          |
| 6     | Frontend unit/hook/component tests (§7.3–7.5)                            | 2–3 weeks | UI dependency upgrades             |
| 7     | GitHub Actions workflows wrapping the local commands (§9)                | 2–3 days  | Automated gating of dependency PRs |
| 8     | E2E suite + test authenticator (§8)                                      | 1–2 weeks | Major-version upgrades             |

Items 2–4 are deliberately front-loaded: they are small and directly de-risk the dependency classes with pending or inevitable major upgrades (EasyRdf, Guzzle 6→7, MUI, TypeScript 4.3→5).
The GitHub Actions migration (item 7) can happen any time after item 1 — it is listed late only because everything before it already works locally; pull it forward if automated gating of Dependabot PRs is urgent.

## 13. Risks and open questions

- **Stardog code reach**: before marking files deprecated (§4.7), verify which production code paths still call `SparqlClient` (e.g. RDF distribution querying, the federated SPARQL controller) so that deprecation does not accidentally cover a feature that *is* still used; if any such path turns out to be live, it moves back into test scope.
- **Castor EDC contract drift**: mocked `ApiClient` responses can rot if the EDC API changes; record real (sanitized) responses as fixtures and refresh them when EDC releases ship.
- **Test-only authenticator** (§8.2) must be reviewed by security; it is standard practice but needs the env gate verified.
- **TypeScript 4.3 / `@types/jest` 26 are old**: Jest setup may force a `@types/jest` bump; add the `yarn typecheck` script *before* the Jest install so type breakage is attributable.
- **Legacy class components** (~15) are harder to test than hooks-based ones; test them through their rendered behavior rather than refactoring first.
- **No CI in the interim**: until the GitHub Actions workflows land, dependency PRs are only as safe as the discipline of running the local commands; this is why the workflows (§12 item 7) should be pulled forward as soon as the first suites exist.
