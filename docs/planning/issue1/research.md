<!-- docs\planning\issue1\research.md -->
<!-- template=research version=8b7bb3ab created=2026-03-06T20:00Z updated=2026-03-06 -->
# Epic 1: Data Fundament — Research

**Status:** COMPLETE
**Version:** 1.1
**Last Updated:** 2026-03-06

---

## Purpose

Establish the research foundation for the data layer upon which all other epics (AI Layer, Weekly Planner,
Training Analysis, Nutrition, AI Coach) depend. This document captures findings, constraints, and design
principles to inform the planning and design phases. No implementation decisions (child issues, build order)
are made here.

---

## Scope

**In Scope:**
Internal data model requirements, storage backend evaluation, adapter framework constraints, ingestion
sources (Garmin export, Garmin Connect API, manual entry), deduplication strategy, embedding strategy,
and applicable engineering principles.

**Out of Scope:**
AI inference, LiteLLM integration, weekly planner UI, nutrition tracking, training analysis algorithms,
authentication/authorisation, implementation task breakdown (planning phase).

---

## Prerequisites

1. Python/FastAPI project structure in `backend/` (scaffolded)
2. SQLite available (Python stdlib — no install required)
3. Garmin data export ZIP from [Garmin account data export](https://www.garmin.com/en-US/account/datamanagement/exportdata/)
4. Garmin Connect API access (OAuth2 via `garth` library — ref: [garth on PyPI](https://pypi.org/project/garth/))
5. `fitparse` library for FIT binary parsing (pure Python, no native deps)
6. `chromadb` package for embedded vector store
7. `alembic` + `sqlalchemy` for schema migrations

---

## Problem Statement

AthleteCanvas needs a unified, extensible data foundation that can ingest activity and health data from
multiple sources (Garmin exports, Garmin Connect API, manual entry), persist it in relational and vector
stores, and distinguish historical records from planned/scenario-driven future records — all without
external infrastructure dependencies.

The core tension is: **three sources, one model**. Each source has a different shape, cadence, and
reliability. The data layer must normalise them into a single internal schema while preserving enough
provenance to reason about data quality and source-of-truth conflicts.

---

## Research Goals

1. Identify the authoritative fields available from each data source (Garmin export, API, manual entry)
2. Evaluate storage backends for a personal-scale, infrastructure-free deployment
3. Determine the right schema shape to support both historical and planned/scenario records
4. Define a source-agnostic adapter contract that enables pluggable ingestion
5. Establish a deduplication strategy that works across overlapping sources
6. Choose an embedding strategy (granularity + model) for semantic retrieval
7. Map applicable SOLID and project coding standards to the architectural constructs of this layer

---

## Background

AthleteCanvas is a personal AI coaching platform. The data layer is its foundation — every other feature
(AI coaching, weekly planning, training load analysis, nutrition) depends on clean, normalised, queryable
training data.

Three ingestion paths exist that must converge into one:

| Path | Trigger | Cadence | Coverage |
|------|---------|---------|----------|
| Garmin data export (ZIP) | Manual, one-off | Bulk historical | Full history since device purchase |
| Garmin Connect API | Scheduled or on-demand | Ongoing, near real-time | Recent and future syncs |
| Manual entry | User-initiated | On-demand | Gap-filling + planned/hypothetical |

The **manual entry path has a dual role**: (1) filling in historical gaps that Garmin doesn't have (e.g.
a race before a Garmin watch, a strength session entered retrospectively), and (2) creating
**planned/scenario records** for future events that the planner and AI coach can reason about. This
dual role is an explicit schema constraint, not an edge case.

---

## Research Findings

### Finding 1 — Garmin Connect API Field Set

The Garmin Connect informal REST API (via `garth`) returns activity summaries with the following
relevant fields. These define the upper bound of what the API adapter can populate:

**Activity core:**
- `activityId` (int) — primary external identifier
- `activityName` (str) — user-set name
- `activityType.typeKey` (str) — e.g. `"running"`, `"cycling"`, `"strength_training"`
- `startTimeLocal` / `startTimeGMT` (ISO8601 strings)
- `timeZoneId` (str)

**Performance metrics:**
- `distance` (float, metres)
- `duration` / `elapsedDuration` / `movingDuration` (float, seconds)
- `averageHR` / `maxHR` (int, bpm)
- `averageSpeed` / `maxSpeed` (float, m/s)
- `calories` (int)
- `elevationGain` / `elevationLoss` (float, metres)
- `steps` / `averageRunningCadenceInStepsPerMinute` / `maxRunCadence` (int)
- `averagePower` / `maxPower` / `normPower` (int, watts — cycling/running power meter)
- `trainingStressScore` (float, TSS)
- `intensityFactor` (float, IF)
- `aerobicTrainingEffect` / `anaerobicTrainingEffect` (float, 0–5)
- `vO2MaxValue` (float, mL/kg/min — Garmin estimate)

**Recovery & readiness (separate endpoints):**
- Daily HRV status: `lastNight` (ms), `weeklyAverage` (ms), `status` (string)
- Body Battery: `charged` / `drained` (int) per day
- Sleep: `totalSleepSeconds`, `deepSleepSeconds`, `remSleepSeconds`, sleepScore

**Conclusion:** The API covers the major performance and recovery fields. Power, TSS, and IF are
only populated when a compatible device/sensor recorded them. The model must accept nullable fields
for device-dependent metrics.

---

### Finding 2 — FIT File Message Types for Training Data

Garmin's bulk export ZIP contains `.fit` binary files per activity (and summary files). The `fitparse`
library can decode them. The following FIT message types are relevant:

| FIT Message | Content | Use |
|---|---|---|
| `session` | Activity-level summary (total time, distance, avg HR, TSS, IF, sport) | Primary source for activity record |
| `record` | Per-second stream (HR, speed, power, cadence, altitude) | Time-series / lap detail |
| `lap` | Lap splits within an activity | Lap-level analysis |
| `hrv` | Inter-beat intervals (IBI in ms) | HRV calculation from individual beats |
| `sleep_level` | Sleep phase per interval (Light, Deep, REM, Awake) | Sleep analysis |
| `monitoring` | Hourly/daily step count, intensity minutes, stress | Daily wellness data |
| `user_profile` | Weight, height, age, max HR settings | Personalisation context |
| `device_info` | Device model, software version | Provenance metadata |

**Conclusion:** At Epic 1 scope, the `session` message is sufficient for activity records. The `record`
(time-series) and `hrv` messages are deferred to the training analysis epic. `sleep_level` and
`monitoring` are in scope for the wellness sub-model of the data layer.

---

### Finding 3 — Storage Backend Evaluation

**Requirement:** Personal-scale (~10 years × ~300 activities/year = ~3,000 activity records plus daily
wellness data ~3,650 rows/year), no infrastructure, single-user, local-first.

| Option | Pros | Cons | Verdict |
|--------|------|------|---------|
| **SQLite + Alembic** | Zero infrastructure, Python stdlib, ACID, full SQL, migration-managed schema | Single-writer (sufficient for personal use) | ✅ **Selected** |
| PostgreSQL | Full SQL, concurrent writes, pgvector extension | Requires server process, over-engineered for personal use | ❌ Rejected |
| TinyDB / JSON files | Simplest possible | No migrations, no relations, no SQL | ❌ Rejected |

**Decision:** SQLite with Alembic migrations. Alembic is non-negotiable — it ensures the schema can
evolve without destructive changes, and it's the project standard for `backend/`.

---

### Finding 4 — Vector Store Evaluation

**Requirement:** Embedded (no server), Python-native, sufficient for personal data scale, upgrade path
if needed.

| Option | Embedded | Upgrade path | Ecosystem | Verdict |
|--------|----------|---|---|---|
| **ChromaDB** | ✅ Yes (DuckDB-backed) | Qdrant or hosted Chroma | Python-native, simple API | ✅ **Selected** |
| Qdrant | ❌ Requires Docker/binary | Qdrant Cloud | High performance, production-grade | ❌ Deferred |
| pgvector | ❌ Requires Postgres | N/A (already Postgres) | SQL-native | ❌ Rejected (SQLite conflict) |
| FAISS | ✅ Library | Limited | Meta/research, no metadata filtering | ❌ Rejected (poor DX) |

**Decision:** ChromaDB embedded. For personal-scale data, this is more than sufficient. Upgrade path
to Qdrant is clean if the project ever needs multi-user or production deployment.

---

### Finding 5 — Embedding Strategy

#### 5a — What the embedding layer is (and is not)

`all-MiniLM-L6-v2` is **not a language model**. It is a sentence transformer: it takes text as input
and returns a fixed-length vector (384 floats). There is no chat, no generation, no token budget. It
runs fully locally, ~80 MB on disk, ~10ms per document on CPU. This is categorically different from
the AI capabilities in Epic 2 (AI Layer).

The embedding layer in Epic 1 has exactly one job: **turn an activity or wellness record into a
vector so it can be retrieved semantically later**. The retrieval (RAG) side lives in Epic 2.

#### 5b — Architecture constraint: ingest model = query model

**Critical:** The model used at ingest time (writing vectors into ChromaDB) must be **identical** to
the model used at query time (searching ChromaDB). If the model changes, all existing vectors are
invalid and must be fully regenerated. This is an immutable property of vector stores.

Consequence: the embedding model is **not a runtime config that can be changed freely**. It is a
**versioned schema decision**. The ChromaDB collection metadata must record:

```json
{
  "embedding_model": "all-MiniLM-L6-v2",
  "embedding_model_version": "1.3.0",
  "dimensions": 384,
  "created_at": "2026-03-06T00:00:00Z"
}
```

Any change to the embedding model requires a migration strategy (re-embed all records into a new
collection, then swap). This is analogous to a breaking database schema migration.

#### 5c — Epic 1 / Epic 2 contract

The embedding model selected here is a **contract between Epic 1 and Epic 2**:

- Epic 1 writes vectors using model X
- Epic 2 queries vectors using model X
- Epic 2 cannot choose a different embedding model without triggering a full re-embed of Epic 1 data

This dependency must be explicit in Epic 2's research document. Epic 2 must accept the embedding
model as an input constraint, not a free choice.

#### 5d — Embedding granularity

Both granularities are valid and non-conflicting:

| Unit | Description | Enables | Phase |
|---|---|---|---|
| **Per-activity** | One vector per `ActivityRecord` | "Find activities like this one", "What did I do before my injury?" | ✅ MVP (Epic 1) |
| **Per-week summary** | One vector per calendar week (training load rollup) | "What did my build weeks look like?", "Compare this week to marathon prep" | Deferred (Epic 2 / 4) |

Per-week vectors are generated from aggregates of activity records — they do not require changing the
per-activity pipeline, they extend it.

#### 5e — Model selection

| Option | Type | Cost | Latency | Privacy | Dims | Verdict |
|--------|------|------|---------|---------|------|---------|
| `all-MiniLM-L6-v2` (sentence-transformers) | Local transformer | Free | ~10ms/doc CPU | Full — no data leaves device | 384 | ✅ **Default** |
| `text-embedding-3-small` (OpenAI via LiteLLM) | API | ~$0.02/1M tokens | API latency | Data leaves device | 1536 | Optional, config-driven |
| `models/text-embedding-004` (Gemini via LiteLLM) | API | Free tier | API latency | Data leaves device | 768 | Optional, config-driven |

**Decision:** Default to `all-MiniLM-L6-v2`. The model is configured via `backend/config/ai.yaml`
(see Finding 9 — Config over Code). Changing the model requires explicit migration, it is not a
hot-swappable setting.

#### 5f — AI tier architecture (scope note)

AthleteCanvas uses AI at multiple layers with different characteristics. The full tier model
(embedding vs. structured extraction vs. reasoning) belongs in the Epic 2 (AI Layer) research
document. For Epic 1, the only AI concern is the embedding layer (5a–5e above). A forward reference
note: Epic 2 must document how LiteLLM routes different task types (embedding, classification,
coaching) to appropriate model tiers, and must accept the Epic 1 embedding model as a fixed input.

---

### Finding 9 — Engineering Principles for the Data Layer

Beyond SOLID (see dedicated section below), the following principles apply explicitly to the data
layer architecture.

#### Config over Code

No operational value — connection strings, file paths, model names, retry counts, thresholds — may
be hardcoded in source files. All such values live in `backend/config/*.yaml` and are loaded at
startup via a typed `AppConfig` Pydantic model.

Concrete examples for the data layer:

```yaml
# backend/config/storage.yaml
sqlite_path: .data/athletecanvas.db
chromadb_path: .data/chroma

# backend/config/ai.yaml
embedding:
  provider: sentence-transformers
  model: all-MiniLM-L6-v2
  dimensions: 384

# backend/config/sync.yaml
garmin_api:
  max_retries: 3
  backoff_base_seconds: 2.0
  rate_limit_cooldown_seconds: 60
```

This enables:
- Switching embedding model by changing one config line (+ triggering migration)
- Moving data directory without touching code
- Overriding via environment variables (12-factor app pattern) in different environments

#### Fail-Fast at Startup

All config values and external dependencies (writable paths, database connectivity) are validated
**before serving any request or starting any pipeline run**. A missing `chromadb_path`, unwritable
`sqlite_path`, or missing required config key results in an immediate exit with a clear error message.

This prevents the failure mode of "import ran for 10 minutes and then crashed because the DB path
was wrong". The rule: **surface configuration errors at startup, not mid-operation**.

#### Idempotence

The import pipeline must produce identical state whether run once or ten times with the same input.
This is a consequence of hash-based deduplication (Finding 6) but must be treated as an explicit
design contract, not a side-effect. Every write operation in the pipeline must be an **upsert**
(insert-or-update), never a blind insert.

This also means the full historical import from a Garmin ZIP can be safely re-run after a schema
migration — it will update existing records, not duplicate them.

#### DRY (Don't Repeat Yourself) — and its relationship to SOLID

DRY and SOLID are complementary, not redundant. SOLID governs **structure** (how responsibilities
are divided), DRY governs **duplication** (the same logic must not exist in two places).

In the data layer, DRY violations most commonly appear as:
- The same datetime normalisation logic in multiple adapters → belongs in a shared `normalise_datetime()` utility
- The same dedup hash calculation in multiple places → belongs in `ActivityRecord` as a class method or validator
- The same retry logic repeated in GarminExportAdapter and GarminApiAdapter → belongs in a shared `RetryStrategy` class

The rule: if you are about to write the same logic in a second place, extract it first.

**Problem:** A user may import a Garmin export ZIP and then also sync the same activities via the
Garmin Connect API. Without deduplication, the same activity would be stored twice.

**Finding:** Hash-based deduplication on a composite key:

```
dedup_hash = sha256(source_type + external_id + start_timestamp_utc)
```

- `source_type`: `"garmin_export"` | `"garmin_api"` | `"manual"`
- `external_id`: Garmin's `activityId` (string) for both Garmin sources; UUID for manual entries
- `start_timestamp_utc`: ISO8601 UTC string (normalised before hashing)

When the same `activityId` appears in both a ZIP export and an API sync, the hash will collide and
the second import updates the existing record (merge strategy: API data takes precedence over export
data where both are present, since API data is fresher and more complete).

Manual entries use a user-assigned or system-generated UUID as `external_id`, so they never collide
with Garmin records.

---

### Finding 7 — Garmin API Failure Handling

**Problem:** The Garmin Connect API is unofficial (no SLA), subject to rate limiting ("429 Too Many
Requests"), and OAuth sessions can expire.

**Finding:**

| Failure mode | Strategy |
|---|---|
| Rate limiting (429) | Exponential backoff with jitter (max 3 retries, 2^n × random(0.5–1.5) seconds) |
| Auth expiry | `garth` handles token refresh automatically; on hard expiry, prompt re-auth |
| Partial sync failure | Track `last_synced_activity_id` in a sync-state table; resume from checkpoint on next run |
| Timeout / network error | Same retry strategy as rate limiting; log and surface to user after max retries |
| Missing fields | All non-core fields are nullable in the internal model; partial records are valid |

**Decision:** The sync adapter maintains a lightweight sync-state record (last successful activity ID,
last sync timestamp, sync status). Partial failures result in a partial import with a logged warning —
they do not rollback what was already persisted.

---

### Finding 8 — Internal Schema Constraints

The following field-level constraints emerged from the multi-source, dual-role architecture:

| Field | Type | Constraint | Rationale |
|---|---|---|---|
| `source` | `Literal["garmin_export", "garmin_api", "manual"]` | Required, immutable | Provenance — never changes after ingestion |
| `record_type` | `Literal["historical", "planned"]` | Required | Dual-role of manual entry; planned activities must be queryable separately |
| `confidence` | `float` (0.0–1.0) | Required, default `1.0` | Manual entries may have lower confidence; planned = `0.0` (not yet occurred) |
| `scenario_id` | `str \| None` | Optional | Groups planned records into a named scenario (e.g. "marathon_prep_2026") |
| `external_id` | `str` | Required | Garmin activityId or user UUID; used in dedup hash |
| `dedup_hash` | `str` | Required, unique index | sha256 composite hash |
| `start_time_utc` | `datetime` (UTC, tz-aware) | Required | Normalised during ingestion; source timezone stored separately |
| `source_timezone` | `str` | Optional | IANA timezone string from source (e.g. `"Europe/Amsterdam"`) |

All datetime fields follow the project standard: **UTC-aware datetime objects** (not naive datetimes,
not local time strings). This is enforced by Pydantic `field_validator` at the DTO boundary.

---

## SOLID Principles Applied to the Data Layer

The data layer is the most critical architectural boundary in AthleteCanvas — every upstream epic
depends on it. The following SOLID principles (from
[docs/coding_standards/CODE_STYLE.md](../../coding_standards/CODE_STYLE.md)) apply explicitly:

### S — Single Responsibility Principle

Each component in the data layer has exactly one reason to change:

- **ActivityRecord DTO**: owns the internal data shape — changes only if the domain model changes
- **BaseAdapter**: owns the adapter contract — changes only if the ingestion interface changes
- **GarminExportAdapter**: owns ZIP + FIT parsing — changes only if Garmin changes its export format
- **GarminApiAdapter**: owns API communication — changes only if the API or auth flow changes
- **ManualEntryAdapter**: owns form/input normalisation — changes only if the manual entry schema changes
- **StorageRepository**: owns SQLite persistence — changes only if the storage backend changes
- **EmbeddingPipeline**: owns vectorisation — changes only if the embedding strategy changes
- **ImportOrchestrator**: owns coordination + deduplication — changes only if the pipeline flow changes

This decomposition means a change in Garmin's API response format touches exactly one file.

### O — Open/Closed Principle

The adapter framework must be **open to extension** (add a new source = add a new adapter class)
and **closed to modification** (adding a source does not change the import pipeline or storage layer).

This is realised by the **source registry pattern**: the pipeline discovers adapters via a registry
(or DI container), not by importing them directly. A new adapter registers itself; the pipeline
requires no changes.

### L — Liskov Substitution Principle

Any concrete adapter (Garmin, API, Manual, or a future Strava adapter) must be a **drop-in
substitute** for the `BaseAdapter` wherever the pipeline accepts an adapter. This means:

- All adapters implement the same `ingest() -> list[ActivityRecord]` signature
- All adapters accept the same configuration interface
- All adapters raise the same exception types for the same failure modes

If a caller can't swap `GarminExportAdapter` for `ManualEntryAdapter` without breaking, the
abstraction is wrong.

### I — Interface Segregation Principle

The data layer exposes separate, focused interfaces rather than a single god-repository:

- **`IActivityReader`**: query interface (fetch by date range, by scenario, by type)
- **`IActivityWriter`**: write interface (upsert, delete)
- **`IEmbeddingStore`**: semantic search interface (similar activities, weekly summaries)
- **`ISyncStateStore`**: sync checkpoint interface (last synced ID, sync status)

Upstream callers (AI layer, planner) depend only on `IActivityReader` — they never touch the write
or sync interfaces. Adapters depend only on `IActivityWriter`. This prevents coupling.

### D — Dependency Inversion Principle

The import pipeline depends on **abstractions** (BaseAdapter, IActivityWriter), never on concrete
implementations:

```
ImportOrchestrator → BaseAdapter (abstract)
                   → IActivityWriter (abstract)
                   → ISyncStateStore (abstract)

# Concrete bindings injected at startup:
GarminExportAdapter → BaseAdapter
SQLiteRepository     → IActivityWriter, IActivityReader, ISyncStateStore
ChromaDBStore        → IEmbeddingStore
```

This means the pipeline can be unit-tested with mock adapters and mock storage — no SQLite or
ChromaDB instance required in tests (aligns with project TDD standards in QUALITY_GATES.md).

---

## Applicable Coding Standards

From [docs/coding_standards/CODE_STYLE.md](../../coding_standards/CODE_STYLE.md):

| Standard | Application |
|---|---|
| **Contract-Driven Development** | All data exchange via Pydantic DTOs — `ActivityRecord`, `WellnessRecord`, `SyncState` etc. No `dict` passing between layers |
| **Full Type Hinting** | All adapter methods, repository methods, and pipeline functions carry full type annotations including return types |
| **Pydantic field_validator for UTC** | All datetime fields validated to UTC-aware at DTO boundary — never store naive datetimes |
| **No global state** | Adapters and repositories receive dependencies via constructor injection, not module-level singletons |
| **Config over Code** | All paths, model names, retry counts in `backend/config/*.yaml`; loaded via typed `AppConfig`; no hardcoded strings in source |
| **Fail-Fast at Startup** | Validate all config and paths before accepting requests; crash early with clear message rather than failing mid-pipeline |
| **Idempotence** | All pipeline writes are upserts; pipeline can be safely re-run with same input |
| **DRY** | Shared utilities for datetime normalisation, dedup hashing, retry logic — never duplicated across adapters |
| **Module headers** | All scaffolded backend modules carry `@layer`, `@dependencies`, `@responsibilities` headers |
| **100-char line length** | Enforced by ruff in CI (Gate 3 in quality gates) |
| **Google-style docstrings** | All public adapter and repository methods documented |
| **Pydantic `json_schema_extra` examples** | All DTOs include at least 2 examples (historical record, planned record) |

From [docs/coding_standards/QUALITY_GATES.md](../../coding_standards/QUALITY_GATES.md):

| Gate | Relevance |
|---|---|
| Gate 0 (ruff format) | All backend/ files formatted before commit |
| Gate 1 (ruff strict lint) | ANN rules enforce type annotations on all adapter/repo methods |
| Gate 4 (mypy strict) | DTOs pass mypy strict — no `Any` types except where explicitly justified |
| Gate 5 (tests passing) | Adapter unit tests use mock adapters and in-memory SQLite |
| Gate 6 (≥90% coverage) | Core DTOs and pipeline logic covered; adapter tests can mock external APIs |

---

## Bridge to Planning Phase

The research has revealed six distinct architectural boundaries within the data layer. Each boundary
is a natural seam along which the work can be divided into independent, mergeable units. The planning
phase will map these groupings to concrete child issues and determine sequencing.

### Grouping A — Foundation: data model + config structure

Everything else depends on two things being locked first: the internal DTO schema (Finding 8 —
`ActivityRecord` field set, `record_type`, `confidence`, `scenario_id`, UTC enforcement) and the
config YAML structure (Finding 9 — `AppConfig`, `storage.yaml`, `ai.yaml`, `sync.yaml`). These are
not independent: the embedding model name, the database paths, and the dedup strategy all live in
config and are referenced by every subsequent component.

**Hard constraint:** this grouping must be complete before any other group starts. Changing the
schema or config structure later triggers rewrites across all adapters and repositories.

### Grouping B — Storage layer: relational + vector stores

SQLite + Alembic (Finding 3) and ChromaDB embedded (Finding 4) are distinct stores but share the
same design contract: they expose typed interfaces (`IActivityWriter`, `IActivityReader`,
`IEmbeddingStore`, `ISyncStateStore`) and receive config via `AppConfig`. They can be developed
in parallel after Group A, but both must be in place before the embedding pipeline (Group E) and
orchestrator (Group F) can run end-to-end.

**Hard constraint:** the ChromaDB collection must record the embedding model in its metadata
(Finding 5b) from creation. This cannot be retrofitted.

### Grouping C — Adapter framework: abstract base + source registry

The Open/Closed principle (SOLID section) requires a `BaseAdapter` abstract class and a source
registry before any concrete adapters are written. The registry pattern is what allows Group D
adapters to be added without modifying the orchestrator. This grouping is thin — it is interface
definition only, no IO logic.

### Grouping D — Concrete adapters: three ingestion paths

Three distinct units with no dependencies on each other, all depending on Group C:

- **Garmin export adapter** — ZIP extraction + FIT `session` message parsing (Finding 2)
- **Garmin Connect API adapter** — `garth` OAuth, activity listing, failure handling (Findings 1, 7)
- **Manual entry adapter** — form normalisation, UUID assignment, `record_type` branching (Finding 8)

Each is fully isolated behind the `BaseAdapter` interface. The export adapter is the highest-value
first target: it provides bulk historical data and requires no network credentials.

### Grouping E — Embedding pipeline

Turns ingested `ActivityRecord` instances into vectors stored in ChromaDB. Depends on Group B
(ChromaDB store must exist) and Group D (records must be available). The pipeline must read the
embedding model from `AppConfig`, not hardcode it (Finding 5, Config over Code). Per-activity
embedding is the MVP scope; per-week rollup vectors are explicitly deferred.

### Grouping F — Import orchestrator: coordination + deduplication

The top-level pipeline that wires Groups C–E together: accepts an adapter, calls `ingest()`,
applies dedup hash logic (Finding 6), upserts to SQLite, triggers embedding, updates sync state.
This grouping is last in dependency order but must be designed with idempotence from the start
(Finding 9) — it is not a wrapper to write quickly at the end.

---

**What planning takes from here:**
These six groupings define the natural issue boundaries. Planning will decide exact scope per issue,
confirm or adjust parallelisation within each group, and produce the dependency graph that drives
the implementation order.

---

## Related Documentation

- [agent.md](../../../agent.md)
- [docs/coding_standards/CODE_STYLE.md](../../coding_standards/CODE_STYLE.md)
- [docs/coding_standards/QUALITY_GATES.md](../../coding_standards/QUALITY_GATES.md)
- [docs/coding_standards/TYPE_CHECKING_PLAYBOOK.md](../../coding_standards/TYPE_CHECKING_PLAYBOOK.md)

---

## Version History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | 2026-03-06 | Agent | Initial scaffold |
| 1.1 | 2026-03-06 | Agent | Answered all open questions; added SOLID mapping; added coding standards table; removed planning content |
| 1.2 | 2026-03-06 | Agent | Rewrote Finding 5 (embedding vs LLM distinction, model versioning, Epic 2 contract); added Finding 9 (Config over Code, Fail-Fast, Idempotence, DRY); updated coding standards table |
