# AthleteCanvas - Agent Cooperation Protocol

**Status:** Active | **Type:** Bootloader | **Context:** AI-powered training, nutrition and weekly planning platform

> **🛑 STOP & READ:** You are an autonomous developer agent. Your goal is precision and efficiency. **DO NOT ask the user for context we already have.** Follow this protocol to orient yourself and begin work.

---

## 🚀 Phase 1: Orientation Protocol

If you need the big-picture MCP server context (vision, architecture, roadmap), read:
- [docs/reference/mcp/mcp_vision_reference.md](docs/reference/mcp/mcp_vision_reference.md)

**Running this protocol allows you to "download" the current project state into your context.**

### 1.1 Tool Activation (Execute FIRST)

> **⚡ CRITICAL:** VS Code Copilot uses lazy loading for MCP tools. Tools appear "disabled" until activated.

**Activate all tool categories before proceeding:**

```
activate_file_editing_tools              → create_file, safe_edit_file, scaffold_artifact (unified tool for code+docs)
activate_git_workflow_management_tools   → 15 git/PR tools (create_branch, git_status, etc.)
activate_branch_phase_management_tools   → phase transition tools
activate_issue_management_tools          → 6 issue tools (create_issue, list_issues, etc.)
activate_label_management_tools          → 5 label tools
activate_project_initialization_tools    → initialize_project, get_project_plan
activate_code_validation_tools           → 4 validation tools
```

**Why:** Tools are dynamically loaded by VS Code based on semantic name analysis. Without activation, they appear as "disabled by user" (misleading error message). This is a VS Code 1.108+ feature (Dec 2025), not part of MCP specification.

### 1.2 State Synchronization (Execute Immediately)

Don't guess the phase or status. **Query the system:**

1. **Read Coding Standards (authoritative source):**
   - [docs/coding_standards/README.md](docs/coding_standards/README.md) — Quick reference and principles overview
   - [docs/coding_standards/CODE_STYLE.md](docs/coding_standards/CODE_STYLE.md) — SOLID, Config over Code, DRY, Idempotence, Fail-Fast, Contract-Driven Development, formatting
   - [docs/coding_standards/QUALITY_GATES.md](docs/coding_standards/QUALITY_GATES.md) — 6 quality gates, ruff/mypy commands, PR rejection criteria
   - [docs/coding_standards/TYPE_CHECKING_PLAYBOOK.md](docs/coding_standards/TYPE_CHECKING_PLAYBOOK.md) — Mandatory typing issue resolution order
2. **Check Development Phase:**
   - `st3://status/phase` → *Tells you current_phase, active_branch, is_clean.*
3. **Check Work Context:**
   - `get_work_context` → *Retrieves active issue, blockers, and recent changes.*

---

## 🏗️ Project Context: AthleteCanvas

**What it is:** A personal AI coach platform integrating training data (Garmin + others), AI-driven analysis (Gemini via LiteLLM), weekly rhythm planning, nutrition tracking, and meal planning.

**Key architectural decisions:**
- **AI layer:** LiteLLM (provider-agnostic, BYOK), Gemini as primary provider
- **AI UX:** Streaming responses — feels like a real coach/sparring partner
- **Data privacy:** User-controlled data scope per AI session (hard filter before prompt injection)
- **Data strategy:** Hybrid RAG + direct injection (structured queries → SQL, open analysis → RAG)
- **Stack:** Python/FastAPI backend, React/Vite frontend, SQLite (local-first)
- **Primary data source:** Garmin export (FIT files), extensible to other sources

**Epics (top-level roadmap):**
| Epic | Domain |
|------|--------|
| Epic 1 — Data Fundament | Garmin import, normalization, storage |
| Epic 2 — AI Layer | LiteLLM integration, streaming, data scope |
| Epic 3 — Weekly Planner | Fixed rhythms, training scheduling, recovery logic |
| Epic 4 — Training Analysis | Dashboards, trends, AI analysis |
| Epic 5 — Nutrition & Meals | Logging, menus, shopping lists, nutritional advice |
| Epic 6 — AI Coach Interface | Chat UI, weekly briefing, proactive insights |

**Git model:** Simple. No milestones. Epics act as milestone-like log. `main` is always stable.

**Language convention:**
- Code, commits, docs → **English**
- Chat with user → **Dutch (Nederlands)**

---

## 🔄 Phase 2: Issue-First Development Workflow

**GOLDEN RULE:** Never commit directly to `main`. All work starts with an issue.

### 2.1 Starting New Work

**Workflow Sequence:**
```
1. create_issue(issue_type, title, priority, scope, body) → Create GitHub issue
2. create_branch         → Create feature/bug/docs/refactor/hotfix branch
3. git_checkout          → Switch to new branch
4. initialize_project    → Set up workflow, phase state, parent tracking
5. get_project_plan      → Verify workflow phases loaded
```

**Example `create_issue` call:**
```python
create_issue(
    issue_type="feature",
    title="Garmin FIT file parser",
    priority="high",
    scope="backend",
    body={
        "problem": "Raw Garmin export ZIP contains FIT files that need parsing into structured activity records.",
        "expected": "Parser reads FIT files and produces normalized ActivityDTO records.",
        "context": "First step of Epic 1 — Data Fundament."
    }
    # Optional: is_epic=True for epics, parent_issue=N for child issues
)
```

**Workflow Types (from `.st3/workflows.yaml`):**

| **feature** | 6 phases: research → planning → design → tdd → integration → documentation | New functionality |
| **bug** | 6 phases: research → planning → design → tdd → integration → documentation | Bug fixes |
| **docs** | 2 phases: planning → documentation | Documentation work |
| **refactor** | 5 phases: research → planning → tdd → integration → documentation | Code improvements |
| **hotfix** | 3 phases: tdd → integration → documentation | Urgent fixes |
| **epic** | 5 phases: research → planning → design → tdd → integration | Large multi-issue initiatives |

**Epic hierarchy:** `main → epic/1-data-fundament → feature/2-garmin-parser, feature/3-normalization`

### 2.2 Phase Progression

```python
transition_phase(branch="feature/2-garmin-parser", to_phase="design")
```

Forced transitions require human approval + documented reason:
```python
force_phase_transition(
    branch="feature/2-garmin-parser",
    to_phase="integration",
    skip_reason="Design phase not needed — implementation is straightforward parsing",
    human_approval="User approved on 2026-03-06"
)
```

### 2.3 TDD Cycle Within Phase

**RED → GREEN → REFACTOR:**

1. **RED:** Write failing test → `git_add_or_commit(workflow_phase="tdd", sub_phase="red", message="add test for X")`
2. **GREEN:** Implement minimum → `git_add_or_commit(workflow_phase="tdd", sub_phase="green", message="implement X")`
3. **REFACTOR:** Clean up → `run_quality_gates(scope="files", files=["path/to/file.py"])` → `git_add_or_commit(workflow_phase="tdd", sub_phase="refactor", message="refactor X")`
4. **Transition:** `transition_phase(to_phase="integration")`

### 2.4 Work Completion

```
1. create_pr(head="feature/2", base="main", title="...", body="...")
2. Wait for human approval (ALWAYS REQUIRED)
3. merge_pr(pr_number=X) - only after human approval
```

---

## 🛠️ Phase 3: Execution Protocols

### A. "Implement a New Component" (DTO, Worker, Service, Adapter)
1. `scaffold_artifact(artifact_type="dto|worker|service", name="ComponentName", context={...})`
2. TDD Loop (Section 2.3)
3. `transition_phase(to_phase="integration")`

### B. "Create Documentation" (Design, Architecture, Research)
1. `scaffold_artifact(artifact_type="design|architecture|research", name="document-name", context={...})`
2. `validate_architecture(scope="all")`

### C. "Manage Labels"
1. `create_label(name="scope:backend", color="...", description="...")`
2. Labels validated against `.st3/labels.yaml`

---

## ⚠️ Phase 4: Critical Directives

1. **Issue-First:** Never work on `main`. Always start with `create_issue`.
2. **Workflow Enforcement:** Always `initialize_project` before work.
3. **TDD is Non-Negotiable:** No code without a test.
4. **Tools > Manual:** Never manually create a file if `scaffold_artifact` exists.
5. **English Artifacts, Dutch Chat:** Code/Docs/Commits in English. Talk to user in Dutch.
6. **Human-in-the-Loop:** PR merge ALWAYS requires human approval.
7. **Quality Gates:** Run before phase transitions and before PR creation.

---

## 🔧 Phase 5: Tool Priority Matrix (MANDATORY)

> **🛑 CRITICAL RULE:** Use MCP tools for ALL operations. NEVER use terminal/CLI where an MCP tool exists.

### Git Operations
| Action | ✅ USE THIS | ❌ NEVER USE |
|--------|-------------|-------------|
| Create branch | `create_branch(branch_type, name, base_branch)` | `run_in_terminal("git checkout -b")` |
| Switch branch | `git_checkout(branch)` | `run_in_terminal("git checkout")` |
| Check status | `git_status()` | `run_in_terminal("git status")` |
| Stage & Commit | `git_add_or_commit(message, workflow_phase?, sub_phase?)` | `run_in_terminal("git add/commit")` |
| Push | `git_push(set_upstream)` | `run_in_terminal("git push")` |
| Pull | `git_pull(rebase)` | `run_in_terminal("git pull")` |
| Fetch | `git_fetch(remote, prune)` | `run_in_terminal("git fetch")` |
| Merge | `git_merge(branch)` | `run_in_terminal("git merge")` |
| Delete branch | `git_delete_branch(branch, force)` | `run_in_terminal("git branch -d")` |
| Stash | `git_stash(action, message)` | `run_in_terminal("git stash")` |
| Restore | `git_restore(files, source)` | `run_in_terminal("git restore")` |
| Diff stats | `git_diff_stat(source_branch, target_branch)` | `run_in_terminal("git diff --stat")` |
| List branches | `git_list_branches(verbose, remote)` | `run_in_terminal("git branch")` |

### GitHub Issues
| Action | ✅ USE THIS | ❌ NEVER USE |
|--------|-------------|-------------|
| Create issue | `create_issue(issue_type, title, priority, scope, body, is_epic?, parent_issue?)` | GitHub CLI / manual |
| List issues | `list_issues(state, labels)` | `run_in_terminal("gh issue list")` |
| Get issue | `get_issue(issue_number)` | `run_in_terminal("gh issue view")` |
| Update issue | `update_issue(issue_number, ...)` | GitHub CLI / manual |
| Close issue | `close_issue(issue_number, comment)` | `run_in_terminal("gh issue close")` |

### GitHub Labels & PRs
| Action | ✅ USE THIS | ❌ NEVER USE |
|--------|-------------|-------------|
| Create label | `create_label(name, color, description)` | GitHub CLI / manual |
| List labels | `list_labels()` | `run_in_terminal("gh label list")` |
| Add labels | `add_labels(issue_number, labels)` | GitHub CLI / manual |
| Create PR | `create_pr(title, body, head, base, draft)` | `run_in_terminal("gh pr create")` |
| List PRs | `list_prs(state, base)` | `run_in_terminal("gh pr list")` |
| Merge PR | `merge_pr(pr_number, merge_method)` | `run_in_terminal("gh pr merge")` |

### Code & Docs Scaffolding
| Action | ✅ USE THIS | ❌ NEVER USE |
|--------|-------------|-------------|
| Any artifact | `scaffold_artifact(artifact_type, name, output_path, context)` | `create_file` with manual code |

**Common artifact types:**
- **Code:** `dto`, `worker`, `service`, `tool`, `schema`, `adapter`
- **Docs:** `design`, `architecture`, `research`, `planning`, `tracking`

**Note:** `output_path` is required for file artifacts. Omitting raises `ERR_VALIDATION`.

### Quality & Testing
| Action | ✅ USE THIS | ❌ NEVER USE |
|--------|-------------|-------------|
| Run tests | `run_tests(path, markers, last_failed_only)` | `run_in_terminal("pytest")` |
| Quality gates | `run_quality_gates(scope, files?)` | `run_in_terminal("ruff/mypy")` |
| Validate template | `validate_template(path, template_type)` | Manual validation |
| Validate architecture | `validate_architecture(scope)` | Manual review |

### File Editing
| Action | ✅ USE THIS | ❌ NEVER USE |
|--------|-------------|-------------|
| Edit file | `safe_edit_file(path, content/line_edits/search+replace, mode)` | Manual editing |
| Create file | `create_file(path, content)` | Manual creation |

### Discovery & Context
| Action | ✅ USE THIS | ❌ NEVER USE |
|--------|-------------|-------------|
| Work context | `get_work_context()` | Manual file reading |
| Search docs | `search_documentation(query, scope)` | grep on docs/ |
| Health check | `health_check()` | N/A |
| Restart MCP server | `restart_server()` | Manual process kill |

---

## 🚫 run_in_terminal Restrictions

**ONLY allowed for:**
- Dev servers: `npm run dev`, `uvicorn app:main --reload`
- Build commands explicitly requested by user
- Package installations: `pip install X`

**FORBIDDEN — use MCP tool instead:**
- File operations, git operations, test execution, quality gates

---

## 🏁 Ready State

- "What is my next task?" → `get_work_context`
- "What phase am I in?" → `st3://status/phase`
- "How do I build X?" → [docs/coding_standards/CODE_STYLE.md](docs/coding_standards/CODE_STYLE.md)
- "Which tool?" → **Phase 5: Tool Priority Matrix**
- "How to start?" → **Phase 2: Issue-First Workflow**

> **Start by running Phase 1.**
