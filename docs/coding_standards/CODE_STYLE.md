# Code Style Guide

## Overview

All code in AthleteCanvas follows these principles, ordered by importance. The earlier a principle appears,
the more foundational it is. When principles appear to conflict, the earlier one wins.

**Principles:**
1. [SOLID](#solid-principles)
2. [DRY — Don't Repeat Yourself](#dry)
3. [Config over Code](#config-over-code)
4. [Idempotence](#idempotence)
5. [Fail-Fast at Startup](#fail-fast-at-startup)
6. [Contract-Driven Development](#contract-driven-development)
7. [Full Type Hinting](#type-hinting)
8. [Code Formatting](#code-formatting)

---

## SOLID Principles

SOLID governs **structure** — how responsibilities are divided between classes and modules.

### S — Single Responsibility Principle
Each class, function, and module has exactly **one reason to change**. If you can name two independent
reasons to modify a file, it has two responsibilities and should be split.

```python
# ❌ WRONG — parser + storage in one class
class GarminImporter:
    def parse_fit_file(self, path: Path) -> list[dict]: ...
    def save_to_database(self, records: list[dict]) -> None: ...

# ✅ CORRECT — separate responsibilities
class FitFileParser:
    def parse(self, path: Path) -> list[ActivityRecord]: ...

class ActivityRepository:
    def upsert_many(self, records: list[ActivityRecord]) -> None: ...
```

### O — Open/Closed Principle
Classes are **open to extension** (new behaviour via new subclass) and **closed to modification**
(existing code unchanged). The primary mechanism is **abstract base classes** and the **registry/DI pattern**
for pluggable components (adapters, services).

```python
# Adding a StravaAdapter never changes ImportOrchestrator or ActivityRepository.
# It registers itself and conforms to BaseAdapter.
```

### L — Liskov Substitution Principle
Any concrete implementation must be a **drop-in replacement** for its abstract type. Callers should
not need to know which concrete class they received.

```python
# ✅ CORRECT — any BaseAdapter subclass works here
def run_import(adapter: BaseAdapter, writer: IActivityWriter) -> ImportResult:
    records = adapter.ingest()
    writer.upsert_many(records)
```

### I — Interface Segregation Principle
Prefer many **focused interfaces** over one broad one. Callers depend only on the methods they
actually use.

```python
class IActivityReader(Protocol):
    def get_by_date_range(self, start: datetime, end: datetime) -> list[ActivityRecord]: ...

class IActivityWriter(Protocol):
    def upsert_many(self, records: list[ActivityRecord]) -> None: ...

# AI layer depends only on IActivityReader — it never touches IActivityWriter.
```

### D — Dependency Inversion Principle
High-level modules depend on **abstractions**, not concrete classes. Concrete bindings are injected
at startup (application entry point), not inside business logic.

```python
# ✅ CORRECT — pipeline receives abstractions
class ImportOrchestrator:
    def __init__(self, adapter: BaseAdapter, writer: IActivityWriter) -> None:
        self._adapter = adapter
        self._writer = writer

# ❌ WRONG — pipeline creates its own dependencies
class ImportOrchestrator:
    def __init__(self) -> None:
        self._adapter = GarminExportAdapter()  # hardcoded concrete
        self._writer = SQLiteRepository()       # hardcoded concrete
```

---

## DRY

DRY governs **duplication** — the same logic must not exist in two places. DRY and SOLID are
complementary: SOLID divides responsibilities, DRY prevents logic from being repeated *within* a
responsibility.

**Common DRY violations in a data layer:**
- Datetime normalisation logic repeated in each adapter → extract to `normalise_datetime()` utility
- Dedup hash calculation in multiple places → belongs as a method on `ActivityRecord`
- Retry logic repeated in multiple adapters → extract to a shared `RetryStrategy` class

**Rule:** before writing something a second time, extract it first.

---

## Config over Code

No operational value may be hardcoded in source files. This includes:
- File paths and database locations
- Model names and AI provider settings
- Retry counts, timeouts, thresholds
- Feature flags

All such values live in `backend/config/*.yaml` and are loaded at startup via a typed Pydantic
`AppConfig` model.

```yaml
# backend/config/storage.yaml
sqlite_path: .data/athletecanvas.db
chromadb_path: .data/chroma

# backend/config/ai.yaml
embedding:
  provider: sentence-transformers
  model: all-MiniLM-L6-v2
  dimensions: 384   # CRITICAL: must match ChromaDB collection — not freely changeable

# backend/config/sync.yaml
garmin_api:
  max_retries: 3
  backoff_base_seconds: 2.0
  rate_limit_cooldown_seconds: 60
```

**Config loading pattern:**
```python
# ✅ CORRECT — config injected via AppConfig
class GarminApiAdapter(BaseAdapter):
    def __init__(self, config: GarminSyncConfig) -> None:
        self._max_retries = config.max_retries

# ❌ WRONG — hardcoded
class GarminApiAdapter(BaseAdapter):
    MAX_RETRIES = 3  # never do this
```

Environment variable overrides follow the 12-factor app pattern and are documented per config key.

---

## Idempotence

The import pipeline must produce identical state whether run once or ten times with the same input.

**Rule:** every write operation is an **upsert** (insert-or-update), never a blind insert.

This guarantees:
- Safe re-run after a schema migration
- Safe re-run after a partial failure
- Predictable state regardless of invocation history

---

## Fail-Fast at Startup

All config values, required file paths, and external dependencies are validated **before** accepting
any request or starting any pipeline run.

```python
# ✅ CORRECT — validate on startup
class StorageConfig(BaseModel):
    sqlite_path: Path
    chromadb_path: Path

    @model_validator(mode="after")
    def validate_paths_are_writable(self) -> "StorageConfig":
        self.sqlite_path.parent.mkdir(parents=True, exist_ok=True)
        if not os.access(self.sqlite_path.parent, os.W_OK):
            raise ValueError(f"sqlite_path parent is not writable: {self.sqlite_path.parent}")
        return self
```

Fail with a clear, actionable error message. Never silently fall back to a default path.

---

## Contract-Driven Development

All data flowing between components must be typed via **Pydantic DTOs**. No raw `dict` passing
between layers.

```python
# ✅ CORRECT — explicit DTO
class ActivityRecord(BaseModel):
    source: Literal["garmin_export", "garmin_api", "manual"]
    record_type: Literal["historical", "planned"]
    ...

def ingest(self) -> list[ActivityRecord]: ...

# ❌ WRONG — primitive types
def ingest(self) -> list[dict]: ...  # no type safety
```

### Pydantic Field Order Convention

1. Primary identifier
2. Classification fields (source, record_type)
3. Core data fields (alphabetical or logical grouping)
4. Optional and nullable fields (at end)

### Pydantic Validators

Use `field_validator` for single-field normalization, `model_validator` for cross-field constraints.

**UTC datetime enforcement (mandatory for all datetime fields):**
```python
@field_validator("start_time_utc", mode="before")
@classmethod
def ensure_utc(cls, v: datetime) -> datetime:
    """Ensure timestamp is UTC-aware."""
    if v.tzinfo is None:
        return v.replace(tzinfo=timezone.utc)
    return v.astimezone(timezone.utc)
```

Never store naive datetimes. All datetimes in the system are UTC-aware.

### json_schema_extra Examples

Required for all exported DTOs. Provide at least 2 examples covering distinct cases:

```python
model_config = {
    "frozen": True,
    "str_strip_whitespace": True,
    "validate_assignment": True,
    "json_schema_extra": {
        "examples": [
            {
                "description": "Historical Garmin activity",
                "source": "garmin_api",
                "record_type": "historical",
                "activity_type": "running",
                "distance_m": 10200.0,
                "start_time_utc": "2026-03-01T07:15:00Z",
            },
            {
                "description": "Planned marathon scenario activity",
                "source": "manual",
                "record_type": "planned",
                "activity_type": "running",
                "distance_m": 42195.0,
                "start_time_utc": "2026-10-18T09:00:00Z",
                "scenario_id": "marathon_prep_2026",
                "confidence": 0.0,
            },
        ]
    },
}
```

---

## Type Hinting

Mandatory for all functions and methods including tests. Use Python 3.10+ union syntax.

```python
# ✅ CORRECT — full type hints, modern syntax
def get_activities(
    source: Literal["garmin_export", "garmin_api", "manual"] | None = None,
    date_from: datetime | None = None,
) -> list[ActivityRecord]: ...

# ❌ WRONG — no hints, old-style Optional
def get_activities(source=None, date_from=None): ...
from typing import Optional, List
def get_activities(source: Optional[str], records: List[ActivityRecord]): ...
```

See [TYPE_CHECKING_PLAYBOOK.md](TYPE_CHECKING_PLAYBOOK.md) for the mandatory resolution order when
typing issues arise.

---

## Code Formatting

### File Headers

All scaffolded Python modules include a standardized header (auto-generated by `scaffold_artifact`).
Manual addition required only for non-scaffolded files:

```python
# backend/adapters/garmin/export_adapter.py
"""
Garmin Export Adapter — ZIP + FIT file ingestion.

Reads a Garmin data export ZIP, parses FIT files using fitparse,
and produces normalized ActivityRecord instances.

@layer: Backend (Adapters)
@dependencies: [fitparse, backend.dtos.activity, backend.config.sync]
@responsibilities:
    - Extract FIT files from export ZIP
    - Parse FIT session messages into ActivityRecord DTOs
    - Normalise source timezone to UTC
"""
```

### Import Organization

Three sections, comment-separated, alphabetical within each:

```python
# Standard library
from datetime import datetime, timezone
from pathlib import Path

# Third-party
from pydantic import BaseModel, Field, field_validator

# Project modules
from backend.config.sync import GarminSyncConfig
from backend.dtos.activity import ActivityRecord
```

Never import inside functions.

### Docstrings

- **Module:** comprehensive (see File Headers above)
- **Class:** one-line summary only
- **Method:** brief and to the point; Google style for anything non-trivial

### Line Length

Maximum **100 characters**. Enforced by ruff (Gate 3).

### Class and Method Size

- **Max method length:** ~20 lines. Extract helpers beyond that.
- **Max nesting depth:** 3 levels. Flatten with early returns.

---

## Anti-Patterns to Avoid

| Anti-pattern | Why | Fix |
|---|---|---|
| Primitive obsession | Dicts instead of DTOs — no type safety | Define a Pydantic model |
| God class | Too many responsibilities | Apply SRP, split the class |
| Magic numbers/strings | `max_retries = 3` hardcoded | Move to config YAML |
| Global state | Module-level singletons | Constructor injection |
| Long functions | Hard to test, reason about | Extract to smaller methods |
| Naive datetimes | Timezone bugs | UTC-aware at DTO boundary |
| Imports in functions | Import order, testability | Always at module top |
| Blind insert (non-idempotent) | Duplicates on re-run | Always upsert |
| Deep nesting | Cognitive overload | Early returns, extraction |

---

## Related Documentation

- [QUALITY_GATES.md](QUALITY_GATES.md) — Pre-merge checklist and ruff/mypy commands
- [TYPE_CHECKING_PLAYBOOK.md](TYPE_CHECKING_PLAYBOOK.md) — Standardized typing issue resolution
