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