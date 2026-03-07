# Coding Standards

## Overview

This directory contains the coding standards for AthleteCanvas. All development — production code and test code alike — must follow these guidelines.

## Quick Reference

| Document | Purpose | When to read |
|---|---|---|
| [CODE_STYLE.md](CODE_STYLE.md) | Formatting, imports, headers, docstrings | Creating new modules |
| [QUALITY_GATES.md](QUALITY_GATES.md) | 7 mandatory gates + test code quality | Before every merge |
| [TYPE_CHECKING_PLAYBOOK.md](TYPE_CHECKING_PLAYBOOK.md) | Standardised mypy/pyright fixes | Fixing type errors |

## Core Principles (priority order)

1. **SOLID** — Single Responsibility, Open/Closed, Liskov, Interface Segregation, Dependency Inversion
2. **DRY** — Don't Repeat Yourself. In production code and test code.
3. **Config over Code** — behaviour driven by config files, never hardcoded
4. **Fail-Fast** — validate at startup, surface errors immediately
5. **Idempotence** — all write operations are safe to repeat
6. **Contract-Driven** — depend on interfaces (`ports/`), never on concrete implementations
7. **TDD** — Red → Green → Refactor. Tests written before implementation.

## Quality Gates Summary

All 7 gates must pass before merging. Gates apply to **both** production and test code:

| Gate | Check | Tool |
|---|---|---|
| 0 | Formatting | `ruff format --check --isolated` |
| 1 | Strict lint | `ruff check --isolated` |
| 2 | Import placement | `ruff check --select=PLC0415` |
| 3 | Line length ≤ 100 | `ruff check --select=E501` |
| 4 | Type checking | `mypy --strict` (domain + ports) |
| 5 | Tests passing | `pytest` |
| 6 | Coverage ≥ 90% | `pytest --cov=backend/athletecanvas --cov-branch` |

See [QUALITY_GATES.md](QUALITY_GATES.md) for exact commands and the **Test Code Quality** section.

## Test Code Non-Negotiables

- **Fake adapters, not mocks** — implement port interfaces with in-memory fakes
- **Fixtures in `conftest.py`** — never inline domain objects per test
- **`conftest.py` per layer** — `tests/unit/` and `tests/integration/` each have their own
- **Config over hardcoded strings** — DSNs, paths, thresholds via fixtures
- **DRY assertions** — extract repeated assertion patterns into helpers
- **Testability is a design signal** — if a service needs a real DB to unit-test, the port is wrong

## Test File Placement

| Source | Test destination |
|---|---|
| `backend/athletecanvas/**` | `tests/unit/<mirror-path>/` or `tests/integration/` |
| Never | `tests/*.py` at root level |


