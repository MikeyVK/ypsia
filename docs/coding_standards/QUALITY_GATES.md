# Quality Gates

## Overview

All Python code in AthleteCanvas must pass **6 mandatory quality gates** before merging to `main`.

> **Primary interface:** Use the MCP tool `run_quality_gates` — it runs all applicable gates for the
> current scope and reports results. Only use the manual commands below for debugging a specific failure.

## Gate Checklist

- [ ] Gate 0: Ruff Format
- [ ] Gate 1: Ruff Strict Lint
- [ ] Gate 2: Import Placement (top-level only)
- [ ] Gate 3: Line Length (≤ 100 chars)
- [ ] Gate 4: Type Checking (Pydantic DTOs — mypy strict)
- [ ] Gate 5: Tests Passing
- [ ] Gate 6: Code Coverage (≥ 90% branch)

---

## Configuration Doctrine: Two-Tier Enforcement

1. **`pyproject.toml`** — IDE Baseline (pragmatic, developer-friendly)
2. **`.st3/quality.yaml`** — CI Authority (strict, used by `run_quality_gates`)

Gates 0–3 run with `--isolated` in CI so they never inherit IDE config.

---

## Gate 0: Ruff Format

Enforces consistent formatting.

```powershell
# Check (no changes written)
python -m ruff format --isolated --check --diff --line-length=100 backend/path/to/file.py

# Apply formatting
python -m ruff format --isolated --line-length=100 backend/path/to/file.py
```

---

## Gate 1: Ruff Strict Lint

```powershell
python -m ruff check --isolated `
  --select=E,W,F,I,N,UP,ANN,B,C4,DTZ,T10,ISC,RET,SIM,ARG,PLC `
  --ignore=E501,PLC0415 `
  --line-length=100 --target-version=py311 `
  backend/path/to/file.py
```

**ANN rules** enforce type annotations on all functions and methods — production and test code alike.

```python
# ❌ WRONG — missing return type in test
def test_activity_record_defaults(record: ActivityRecord):
    assert record.confidence == 1.0

# ✅ CORRECT
def test_activity_record_defaults(record: ActivityRecord) -> None:
    assert record.confidence == 1.0
```

---

## Gate 2: Import Placement

All imports must be at module top-level — never inside functions.

```powershell
python -m ruff check --isolated --select=PLC0415 --target-version=py311 backend/path/to/file.py
```

---

## Gate 3: Line Length

Maximum 100 characters.

```powershell
python -m ruff check --isolated --select=E501 --line-length=100 --target-version=py311 backend/path/to/file.py
```

**Fix techniques:**
```python
# Long assertion → intermediate variable
field_value = record.start_time_utc
assert field_value.tzinfo is not None

# Long string → implicit concatenation
message = (
    "Import failed: the embedding model configured in ai.yaml does not match "
    "the model recorded in the ChromaDB collection metadata."
)
```

---

## Gate 4: Type Checking (DTOs)

Applies to Pydantic DTO files. See [TYPE_CHECKING_PLAYBOOK.md](TYPE_CHECKING_PLAYBOOK.md) for
the mandatory resolution order.

```powershell
python -m mypy backend/dtos/activity.py --strict --no-error-summary
```

Test files are exempt from mypy strict (known Pydantic FieldInfo false positives — use `getattr`
pattern, see TYPE_CHECKING_PLAYBOOK.md §A).

---

## Gate 5: Tests Passing

```powershell
pytest tests/unit/path/to/test_file.py -q --tb=line
```

All tests must pass. 100% pass rate required — no skips without documented justification.

---

## Gate 6: Code Coverage

Branch coverage ≥ 90% across production packages.

```powershell
pytest tests/ --cov=backend --cov-branch --cov-fail-under=90 --tb=short
```

**Coverage scope:** `backend/` only. When new top-level packages are added, extend `--cov` accordingly.

**Why separate from Gate 5?**
- Gate 5: validates correctness (do tests pass?)
- Gate 6: validates thoroughness (are all paths tested?)

---

## Post-Implementation Workflow

```powershell
# 1. Format
python -m ruff format --isolated --line-length=100 backend/path/to/file.py

# 2. Lint + fix
python -m ruff check --fix --isolated --select=E,W,F,I,N,UP,ANN,B,C4,DTZ,T10,ISC,RET,SIM,ARG,PLC --ignore=E501,PLC0415 --line-length=100 --target-version=py311 backend/path/to/file.py

# 3. Type check (DTOs only)
python -m mypy backend/dtos/your_dto.py --strict --no-error-summary

# 4. Run tests
pytest tests/unit/path/test_file.py -q --tb=line

# 5. Coverage
pytest tests/ --cov=backend --cov-branch --cov-fail-under=90 --tb=short
```

> **Prefer:** `run_quality_gates(scope="files", files=["backend/path/to/file.py"])` over running
> the above manually.

---

## pyrightconfig.json

```json
{
  "pythonVersion": "3.11",
  "typeCheckingMode": "basic",
  "reportUnknownMemberType": false,
  "reportUnknownVariableType": false,
  "reportCallIssue": false,
  "reportArgumentType": false,
  "reportAttributeAccessIssue": false
}
```

Disabled checks suppress Pydantic-specific false positives. Do not relax beyond this baseline
without team discussion.

---

## Known Acceptable Warnings

### Pydantic FieldInfo in Tests

Pylance treats Pydantic fields as `FieldInfo` at the type level even though they resolve to values
at runtime.

```python
# ✅ PREFERRED — use getattr()
assert getattr(record, "source") == "garmin_api"

# ✅ ACCEPTABLE — intermediate variable
source = str(record.source)
assert source == "garmin_api"
```

### Pydantic Generics + Field

```python
activities: list[ActivityRecord] = Field(default_factory=list)  # type: ignore[valid-type]  # pydantic typing edge
```

### Optional Fields

```python
# Prefer narrowing over cast
value = record.scenario_id
assert value is not None
assert value.startswith("marathon_")
```

---

## Code Review Rejection Criteria

A PR will be rejected if:
- Any gate fails (exit code non-zero)
- `dict` used instead of a Pydantic DTO for inter-layer data
- Hardcoded config values (paths, model names, thresholds) in source files
- Naive datetimes stored or passed between components
- Non-idempotent writes (blind `INSERT` without `ON CONFLICT DO UPDATE`)
- Logic duplicated across two files instead of extracted to a shared utility

---

## Related Documentation

- [CODE_STYLE.md](CODE_STYLE.md) — Principles and conventions
- [TYPE_CHECKING_PLAYBOOK.md](TYPE_CHECKING_PLAYBOOK.md) — Typing resolution order
