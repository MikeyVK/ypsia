<!-- docs\planning\issue16\research.md -->
<!-- template=research version=8b7bb3ab created=2026-03-07T10:34Z updated= -->
# Foundational Architecture Research

**Status:** DRAFT  
**Version:** 1.0  
**Last Updated:** 2026-03-07

---

## Purpose

Establish the architectural foundation that all current and future epics depend on. This research supersedes single-user assumptions made in Epic 1 research and will drive a revised epic list and child issue updates.

## Scope

**In Scope:**
Project scaffolding, multi-tenancy (user model, data isolation), authentication (JWT, MFA, OAuth), API-first design, command/query service layer, frontend platform strategy, hexagonal architecture package layout

**Out of Scope:**
Implementation of any component (that belongs to child issues), AI layer design (Epic 2), deployment/infrastructure (later epic), specific UI design/wireframes

## Prerequisites

Read these first:
1. Epic 1 research.md v1.2 — findings remain valid except Finding 3 (SQLite, now superseded)
2. Epic 1 design brainstorm — architectural gaps identified there are the direct input for this research
---

## Problem Statement

Critical architectural gaps discovered during Epic 1 design brainstorm: no project scaffolding baseline exists, multi-user and multi-platform requirements are unaddressed, the frontend data ingestion layer is entirely missing, the command/query service layer connecting frontend to backend is undefined, and auth (JWT, MFA, OAuth per-user) has not been designed. These gaps affect all current and future epics.

## Research Goals

- Determine project scaffolding baseline: package layout, pyproject.toml, import conventions, Alembic env, test structure
- Evaluate and decide relational storage: PostgreSQL vs SQLite for multi-user requirements
- Determine ChromaDB user isolation strategy: per-user collection vs metadata filter
- Research auth stack: JWT, MFA (WebAuthn vs TOTP), Garmin OAuth per-user with PKCE for mobile
- Define API-first design principles: versioning, OpenAPI, CORS, platform-agnostic endpoint design
- Design command/query service layer (CQRS-light) connecting FastAPI routes to domain services
- Define frontend platform strategy: web-first with mobile-extensible design, PWA, React Native path
- Determine hexagonal architecture package structure for multi-user, multi-platform backend
- Assess impact of multi-tenancy on existing Epic 1 child issues #7-#15

---

## Background

Epic 1 was designed assuming single-user SQLite. During design brainstorm, three critical gaps emerged: (1) no project scaffolding exists as a cross-epic foundation; (2) multi-user + multi-platform (web, Android, iOS) + MFA requirements invalidate the single-user storage and auth assumptions; (3) the frontend layer for data ingestion and the command/query service connecting it to the backend are entirely absent from the design.

## Open Questions

- ❓ PostgreSQL vs SQLite: what is the exact migration path and dev experience tradeoff?
- ❓ ChromaDB: per-user collection naming convention and lifecycle management?
- ❓ WebAuthn: which Python library, what is the registration/authentication flow, recovery strategy?
- ❓ PKCE flow: how does garth handle mobile OAuth callbacks, what are the deep link requirements?
- ❓ CQRS-light: how do Commands and Queries map to FastAPI routes and internal service calls?
- ❓ React Native + Expo: what is the monorepo strategy relative to the existing Vite frontend?
- ❓ User model: what fields are required, where does it live in the hexagonal structure?
- ❓ Frontend Framework epic: what is the minimal scope to unblock all feature epics?


## Findings

### Finding 1 — Project Scaffolding & Monorepo Structure

**Decision: flat hexagonal layout, pnpm monorepo, no src-layer, no root pyproject.toml.**

#### Repository layout

```
/ (repo root)
  backend/
    athletecanvas/
      domain/          # ActivityRecord, User, AppConfig — pure logic, no IO
      ports/           # IActivityWriter, IEmbeddingStore, BaseAdapter — interfaces only
      services/        # ImportOrchestrator, EmbeddingPipeline, AuthService — use cases
      adapters/
        inbound/       # FastAPI routes, request/response schemas
        outbound/      # PostgreSQLRepo, ChromaDBStore, GarminAdapter, etc.
    tests/
      unit/
      integration/
    alembic/
    alembic.ini
    pyproject.toml     # sole Python entry point
  frontend/            # React + Vite (web)
    src/
    package.json
  mobile/              # React Native + Expo — from day one
    src/
    package.json
    app.json
  shared/              # OpenAPI-generated client types — consumed by frontend + mobile
    api/
    package.json
  docs/
  .st3/
  package.json         # pnpm workspace root
  pnpm-workspace.yaml  # declares frontend/, mobile/, shared/
  Makefile             # cross-language convenience: make test, make lint, make dev
  .python-version      # pyenv/uv Python version pin for the entire repo
```

#### Rationale

- **No `src/` layer** — `src/` is a Python packaging convention for published libraries. For a FastAPI application it adds indirection without benefit. `backend/athletecanvas/` directly is simpler and unambiguous.
- **Hexagonal layers** — dependency rule: everything points inward. `domain/` and `ports/` never import from `services/` or `adapters/`. `services/` depends on `ports/`, never on concrete adapters. `adapters/outbound/` implements `ports/`. `adapters/inbound/` (FastAPI routes) calls `services/`.
- **`services/` inside `athletecanvas/`** — application-level use cases, not HTTP handlers. They orchestrate domain logic via ports. Testable without a running server.
- **`shared/` from day one** — OpenAPI spec → codegen → `shared/api/` → both `frontend` and `mobile` import the same typed client. Prevents drift without extra effort per endpoint.
- **pnpm workspaces** — manages `frontend/`, `mobile/`, `shared/` as a single workspace. Faster and cleaner than npm workspaces for monorepos.
- **No root `pyproject.toml`** — Python has no native workspace support. A root-level Python config creates ambiguity. If a second Python package is added later (e.g. `scripts/`), a `uv` workspace can be introduced at that point (YAGNI).
- **`Makefile` as cross-language entrypoint** — `make test`, `make lint`, `make dev` abstract over `pytest` vs `pnpm` vs `docker compose`. Single command surface for CI and developers.
- **Tests in `backend/tests/`** — separate from source, split into `unit/` and `integration/`. Compatible with Alembic env setup and standard pytest discovery.

#### Cross-epic impact

This structure is the prerequisite for all epics. Every child issue in every epic operates within this layout. The `shared/` package means any endpoint added in any epic is immediately available to both web and mobile without additional work.

### Finding 2 — Relational Storage: PostgreSQL + SQLAlchemy Core + Alembic

**Decision: PostgreSQL as the sole relational store, SQLAlchemy Core for repositories, Alembic for migrations, pytest-postgresql for integration tests.**

#### Why PostgreSQL over SQLite

SQLite is a single-writer database. With multiple users importing data concurrently, write-lock contention is inevitable and unrecoverable without an architectural rewrite. PostgreSQL is the baseline for any multi-user application. SQLite remains valid for local unit test fixtures only (in-memory, no disk I/O).

#### ORM strategy: SQLAlchemy Core (not ORM)

In hexagonal architecture, domain models (Pydantic) must remain independent of persistence concerns. SQLAlchemy ORM couples models to table definitions — a known anti-pattern in hexagonal design. SQLAlchemy Core keeps the two separate:

- **Domain model** (`domain/models.py`) — pure Pydantic, no ORM decorators
- **Table definition** (`adapters/outbound/storage/tables.py`) — SQLAlchemy `Table` objects, internal detail of the adapter
- **Repository** (`adapters/outbound/storage/postgresql.py`) — maps between the two explicitly

asyncpg (raw SQL, no ORM) was considered and rejected: it requires abandoning Alembic, which is non-negotiable for a schema that will evolve across many epics.

#### Alembic for migrations

Alembic manages schema evolution the same way Git manages code: every change is a versioned, reversible migration stored in `backend/alembic/versions/`. The database carries its own version pointer (`alembic_version` table). `alembic upgrade head` is idempotent and safe to run in CI/CD pipelines. Manual SQL migration management across multiple epics is rejected as a high-risk approach.

#### Test strategy: two-tier

- **Unit tests** (`tests/unit/`) — zero DB dependency. All services (`services/`) are tested via fake adapters implementing the port interfaces (e.g. `FakeActivityWriter(IActivityWriter)`). Fakes are first-class citizens, not throwaway mocks.
- **Integration tests** (`tests/integration/`) — `pytest-postgresql` spins up a real PostgreSQL process per test session. No dialect mismatch possible. No Docker daemon required in CI.

```
make test-unit    → pytest tests/unit/       (no DB, fast)
make test-int     → pytest tests/integration/ (pytest-postgresql)
make test         → both
```

#### Test code quality is production code quality

Tests are subject to the same SOLID, DRY, and Config-over-Code principles as production code. This is non-negotiable and enforced by quality gates:

- **Fixtures are ports** — `ActivityRecordFactory`, `UserFactory` are shared builders in `conftest.py`, never inline dicts repeated per test
- **Fake adapters, not mocks** — `FakeActivityWriter(IActivityWriter)` is a real class with an in-memory list. `unittest.mock.patch()` is fragile on refactor; fakes are not
- **`conftest.py` per layer** — `tests/unit/conftest.py` and `tests/integration/conftest.py` are separate. No single root-level conftest becoming a dumping ground
- **Config over hardcoded values** — DSNs, model paths, feature flags via pytest fixtures, never hardcoded strings scattered across test files
- **DRY assertions** — reusable assertion helpers (`assert_activity_equals(a, b)`) instead of 15 repeated `assert` statements per test
- **Quality gates apply equally** — ruff, type checking, coverage thresholds cover `tests/` identically to `athletecanvas/`

This principle extends to design: if a service cannot be tested with a fake adapter implementing a port, the port interface is wrong. Testability is a design signal.

#### Local dev setup

```
docker compose up -d  # starts PostgreSQL for local development
alembic upgrade head  # applies all pending migrations
```

`make dev` abstracts this. No local PostgreSQL installation required.

### Finding 3 — Project Onboarding & Reference Docs Technical Debt

**Decision: a single project README.md at repo root + a lean agent.md covering the full project. Reference docs scope-locked to AthleteCanvas. Addressed in a dedicated scaffolding child issue.**

#### The problem

A fresh agent or developer starting on this project today has no single entry point. `docs/coding_standards/` existed but referenced S1mpleTrader V3 throughout, linked to non-existent files (`TDD_WORKFLOW.md`, `GIT_WORKFLOW.md`, `docs/architecture/`, `docs/reference/`), and contained MCP server-specific guardrails from a different project. This creates immediate context pollution for any agent that reads these docs.

#### What good onboarding requires

1. **Root `README.md`** — one file, answers: what is this project, how do I run it locally, what is the architecture in one paragraph, where are the docs
2. **`agent.md`** — compact, no duplication with README. Links to coding standards, explains the MCP workflow, lists the epics. Agent context budget is limited — every redundant line is wasted
3. **`docs/coding_standards/`** — already cleaned up in this branch: S1mpleTrader refs removed, dode links removed, test code quality added, README rewritten as lean quick reference
4. **No orphaned reference docs** — removed all links to `TDD_WORKFLOW.md`, `GIT_WORKFLOW.md`, `docs/architecture/`, `docs/reference/`, `docs/implementation/` that do not exist

#### What remains to be done (scaffolding epic child issue)

- Root `README.md` — does not yet exist for AthleteCanvas
- `agent.md` — exists but needs a full review pass: remove S1mpleTrader context, align with new epic structure, add links to this foundational research and Epic 1 research

These are implementation tasks for the scaffolding child issue, not research findings. They are captured here to ensure they are not forgotten.

#### Principle: agent context is a scarce resource

`agent.md` must follow the same Config-over-Code principle applied to code: reference, don't duplicate. A 50-line `agent.md` that points to the right docs is more valuable than a 500-line `agent.md` that tries to contain everything and goes stale.

### Finding 4 — ChromaDB User Isolation Strategy

**Decision: per-user collection named `activities_{user_uuid}`, eagerly created at registration, shared embedding model as a platform-level contract.**

#### Per-user collection over metadata filter

Each user gets a dedicated ChromaDB collection (`activities_{user_uuid}`). The alternative — one shared collection with a `user_id` metadata filter on every query — was rejected on security grounds: a missing filter is a data leak (OWASP A01 Broken Access Control). With per-user collections, isolation is structural, not conditional. GDPR deletion is also trivial: drop the collection.

#### Collection naming: UUID not integer PK

Collection names use the user's UUID (`activities_550e8400-e29b-41d4-a716-...`), not the integer primary key. UUIDs are non-enumerable — a collection name exposed in a log, an error message, or a misconfigured endpoint cannot be used to access another user's data. Integer PKs are sequential and guessable.

The UUID is always available from the authenticated user context, so no extra DB lookup is required at query time.

#### Lifecycle: eager creation at registration

The collection is created when the user account is created, not lazily on first embedding. This eliminates null-checks and error branches throughout the embedding pipeline — if the user exists, the collection exists. Deletion is part of the account deletion transaction: PostgreSQL user record and ChromaDB collection are removed together.

#### Embedding model: one model, one language, one knowledge base

All user collections are created with identical embedding model metadata (model name, version, dimensions from `AppConfig`). This is a deliberate platform-level architectural principle, not a technical constraint.

When every user's activities are embedded by the same model, all vectors occupy the same coordinate space. A 45-minute run at 158bpm produces a comparable vector for every user. This makes cross-user patterns discoverable: finding users with similar training loads, community benchmarks, anomaly detection. The moment different users use different models, their data becomes mutually incomprehensible — not one platform with many users, but many isolated islands that happen to share an app.

The embedding model is the shared language of the platform. Changing it is a migration event affecting all users simultaneously — not a per-user configuration option.

### Finding 5 — Authentication & Identity: Delegated to Ory Kratos

**Decision: Ory Kratos handles all identity management. FastAPI is a pure resource server. Zero custom auth code.**

#### Why delegated identity, not DIY

Building authentication from scratch — WebAuthn registration flows, TOTP seed generation, refresh token rotation, account recovery — is high-risk, high-maintenance work that is not differentiating. Every hour spent on auth is an hour not spent on the core platform. More importantly, rolling custom auth is one of the most reliable ways to introduce security vulnerabilities (OWASP A07 Identification and Authentication Failures).

Ory Kratos is a self-hosted, open-source (Apache 2.0) identity and user management system written in Go. It is not a SaaS product — no paid dependency, no data leaving the infrastructure.

#### What Ory Kratos handles

- **Passkeys / WebAuthn** — primary MFA, FIDO2 compliant, device-bound credentials
- **TOTP** — fallback MFA (Google Authenticator, Authy), also used as account recovery codes
- **Registration & login flows** — Kratos exposes headless API flows; the React frontend drives the UI
- **Account recovery** — built-in recovery via email OTP or backup codes; no custom flow needed
- **Session management** — Kratos issues sessions; FastAPI validates them via the Ory `check` endpoint or JWT introspection
- **React Native** — `@ory/client` SDK works on React Native; no WebAuthn bridge required at the application layer

#### FastAPI as a pure resource server

FastAPI receives requests with a `Bearer` token or session cookie. It calls the Ory `toSession` endpoint (or validates a JWT signed by Ory) to verify identity. If valid, it extracts `user_uuid` from the token and proceeds. If not, it returns 401.

FastAPI owns **zero** user credentials, **zero** password hashes, **zero** WebAuthn state. The `users` table in PostgreSQL contains only application-level data: `user_uuid` (foreign key to Ory's identity ID), preferences, consent records, linked data-source tokens. No auth state lives in the application DB.

```
Client → Ory Kratos (login/register/MFA) → session token
Client → FastAPI (resource requests + Bearer token) → Ory /sessions/whoami → user_uuid → application logic
```

#### Third-party data source tokens (Garmin, Strava, etc.)

OAuth2 tokens for external data sources (Garmin SSO session via `garth`, Strava OAuth2, etc.) are **application-level** secrets, not identity credentials. They are stored encrypted in PostgreSQL under the `user_uuid`. Ory Kratos does not manage these — this is the application's responsibility.

Encryption at rest: AES-256-GCM, key from `AppConfig` (environment variable, never hardcoded). Token fields are encrypted before insert, decrypted after fetch — handled by a dedicated `ITokenStore` port.

#### No paid dependencies

All components are free and open-source:

| Component | License | Role |
|---|---|---|
| Ory Kratos | Apache 2.0 | Identity & MFA |
| Ory Hydra (optional) | Apache 2.0 | OIDC provider (if AthleteCanvas ever issues tokens to third parties) |
| `@ory/client` | MIT | React + React Native SDK |

Ory Cloud (the hosted SaaS version) is explicitly **not used**. Kratos runs as a Docker container alongside the application.

#### Mobile considerations

React Native uses `@ory/client` for all auth flows. The Kratos SDK handles passkey flows via platform APIs (iOS Face ID / Touch ID, Android Biometrics). On devices without biometric support, TOTP is the fallback. There is no custom WebAuthn bridge in the application layer.

### Finding 6 — Data Ingestion Architecture

**Decision: `TrackingRecord` as the universal domain envelope, `IDataSource` port per external source, hybrid PostgreSQL storage (fixed columns + JSONB payload), ARQ for background jobs, `garth` polling as the primary Garmin data path.**

#### The core principle: uniform pipeline, diverse payload

The ingestion pipeline has one job regardless of whether a GPS activity, a sleep record, a weight measurement, or a user settings sync arrives. The diversity lives in the payload, not in the pipeline. This separates two concerns that must not be entangled: *how data moves through the system* (pipeline) and *what the data looks like* (schema per type).

#### Domain model: `TrackingRecord`

`TrackingRecord` is the universal envelope. It is not "health data" — it is any data that is tracked over time for a user. The discriminator `record_type` determines how the payload is interpreted.

```
TrackingRecord
├── record_type: RecordType      # discriminator enum
├── user_uuid: UUID
├── source_id: str               # "garmin", "strava", "polar", "manual"
├── external_id: str             # source's own ID — deduplication key
├── recorded_at: datetime        # when the event occurred (not ingested)
├── ingested_at: datetime        # when we stored it
├── payload: dict                # type-specific data, Pydantic-validated
└── is_embeddable: bool          # whether this record goes to ChromaDB

RecordType:
  ACTIVITY       # GPS, HR, power, cadence, elevation
  SLEEP          # stages, HRV, SPO2, duration, score
  BODY_METRICS   # weight, body fat, VO2max, bone mass
  USER_SETTINGS  # HR zones, FTP, age, height, units preference
  USER_PROFILE   # static/sensitive: blood type, medical notes — encrypted
```

Pydantic discriminated unions validate the payload at ingestion boundary. If a payload does not conform to its declared `record_type` schema, it is rejected before touching PostgreSQL.

#### PostgreSQL storage: hybrid fixed + JSONB

A single `tracking_records` table with fixed columns for indexed/constrained fields and JSONB for the type-specific payload:

```sql
CREATE TABLE tracking_records (
    id            BIGSERIAL PRIMARY KEY,
    user_uuid     UUID NOT NULL REFERENCES users(user_uuid),
    record_type   TEXT NOT NULL,
    source_id     TEXT NOT NULL,
    external_id   TEXT NOT NULL,
    recorded_at   TIMESTAMPTZ NOT NULL,
    ingested_at   TIMESTAMPTZ NOT NULL DEFAULT now(),
    payload       JSONB NOT NULL,
    UNIQUE (user_uuid, source_id, external_id)
);
```

The `UNIQUE (user_uuid, source_id, external_id)` constraint makes every sync operation idempotent: `INSERT ... ON CONFLICT DO NOTHING`. The same poll can run ten times without data corruption or duplicate records.

New `RecordType` values require no migration — only a new Pydantic model for payload validation, a corresponding `is_embeddable` rule, and (if indexed queries are needed on a payload field) an optional functional index on the JSONB column.

#### What goes to ChromaDB

Not all `TrackingRecord` types carry semantic meaning suitable for embedding:

| RecordType | PostgreSQL | ChromaDB |
|---|---|---|
| `ACTIVITY` | ✅ | ✅ semantic search, pattern matching |
| `SLEEP` | ✅ | ✅ longitudinal patterns |
| `BODY_METRICS` | ✅ | ❌ time series, no semantic value |
| `USER_SETTINGS` | ✅ | ❌ |
| `USER_PROFILE` | ✅ (encrypted fields) | ❌ |

`is_embeddable=True` on a `TrackingRecord` triggers automatic queuing for the embedding pipeline after PostgreSQL persist. The embedding pipeline is decoupled from ingestion — it reads from a queue, not from the ingestion path directly.

#### The `IDataSource` port

```python
class IDataSource(Protocol):
    source_id: str  # matches source_id in TrackingRecord

    async def fetch_since(
        self, user: UserContext, since: datetime
    ) -> list[RawRecord]: ...

    async def normalize(self, raw: RawRecord) -> TrackingRecord: ...

    async def check_connection(self, user: UserContext) -> ConnectionStatus: ...
```

The pipeline calls only this interface. Adding a new data source = one new adapter class implementing `IDataSource`. No pipeline changes, no new tables.

#### Garmin via `garth`: polling as the primary data path

`garth` provides access to raw Garmin data: FIT files, per-second HR streams, full GPS tracks, historical data as far back as Garmin stores. This is the data AthleteCanvas needs for embedding and analysis. The Garmin Health API (webhook/push partnership) delivers only processed summaries and does not expose raw FIT files or detailed streams — it is a subset of what `garth` provides.

`garth` polling is therefore the primary Garmin adapter, not a temporary workaround. Each poll:
1. Loads the encrypted `garth` session from `ITokenStore`
2. Fetches records since last successful sync timestamp
3. On `SessionExpiredError`: marks source as `disconnected`, enqueues user notification ("Reconnect Garmin"), aborts
4. On success: normalizes to `TrackingRecord` list, writes updated session state back to `ITokenStore`

The Garmin Health API is an optional future upgrade path: it could serve as a real-time webhook trigger that initiates a `garth` fetch immediately rather than waiting for the next scheduled poll. This requires no architectural changes — it is one more trigger type feeding the same queue.

#### Background task queue: ARQ over Celery

ARQ (async-first Redis queue, MIT license) is chosen over Celery. Celery was designed for synchronous Python and adds broker-configuration complexity that does not fit the async FastAPI stack. ARQ integrates natively with `asyncio`, requires only Redis, and is sufficient for the load profile of a personal/small multi-user platform.

Three trigger paths for sync jobs:

| Trigger | Path | Notes |
|---|---|---|
| **Scheduled** | ARQ cron, configurable interval (default 30 min) per active user + source | Runs even when user is not active |
| **User-initiated** | `POST /sync/{source_id}` → enqueue → `202 Accepted` | UI "refresh" button |
| **Activation-based** | App foreground event → API call → enqueue | Debounced: max 1 job per user per source per 60s to prevent queue flooding |

#### Adapter implementation priority

1. **Garmin (`garth` polling)** — first working demo; existing personal data available immediately
2. **Strava (OAuth2 + webhook)** — real-time, second largest activity dataset
3. **Polar / Fitbit / Withings** — same OAuth2 + webhook pattern as Strava; marginal cost per additional adapter is low once the webhook receiver infrastructure exists
4. **Apple Health / Google Health Connect** — native SDK adapters, mobile-only, separate epic

## Related Documentation
- **[docs/planning/issue1/research.md][related-1]**
- **[docs/planning/issue1/planning.md][related-2]**
- **[docs/coding_standards/README.md][related-3]**

<!-- Link definitions -->

[related-1]: docs/planning/issue1/research.md
[related-2]: docs/planning/issue1/planning.md
[related-3]: docs/coding_standards/README.md

---

## Version History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 |  | Agent | Initial draft |