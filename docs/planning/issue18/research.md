<!-- docs/planning/issue18/research.md -->
<!-- template=research version=8b7bb3ab created=2026-03-10T17:56Z updated=2026-03-10 -->
# Research: Marketing Site — React+Vite Refactor of PHP Prototype

**Status:** APPROVED
**Version:** 1.1
**Last Updated:** 2026-03-10
**Issue:** [#18](https://github.com/MikeyVK/ypsia/issues/18)
**Parent:** [#17](https://github.com/MikeyVK/ypsia/issues/17)

---

## Problem Statement

The PHP prototype on `feat/17-marketing-site` contains two Principle 5 violations: `cdn.tailwindcss.com` (external CDN runtime dependency) and `fonts.googleapis.com` (Google tracking via font load, also violates Principle 1). PHP is also the wrong stack — the existing `frontend/` scaffold (React 18.3.1 + Vite 5.4.10) is the correct foundation. The refactor replaces the PHP approach entirely and anchors charter compliance architecturally in the build step.

---

## Research Goals

1. Define exact scope: which pages and features to build, based on PHP prototype
2. Identify all charter violations in the prototype and their structural fixes
3. Determine design token inventory to preserve visual parity
4. Assess charter rendering complexity and decide on correct rendering strategy
5. Define routing, form handling, and analytics integration approach

---

## Findings

### 1. Scope — Exactly Two Pages

The PHP prototype defines two routes, nothing more:

| Route | PHP file | Description |
|-------|----------|-------------|
| `/` | `index.php` | Landing page |
| `/charter` | `charter.php` | Charter manifest renderer |

**Landing page sections (in order):**
1. Hero — headline, two-paragraph body copy, feature panel (three items with icons)
2. Charter CTA block — card linking to `/charter`
3. Waitlist section — subscribe form (email + interest field) + unsubscribe form, with feedback states (`success`, `already`, `error`, `removed`)

**Charter page sections:**
- Sticky `manifest` header with TOC toggle button (hamburger)
- TOC flyout panel (left-anchored, backdrop, keyboard dismiss via Escape)
- `<article>` with full charter prose
- Back-to-top button (fixed, bottom-right)

No dashboard, no login, no user state. Scope is intentionally minimal.

---

### 2. Charter Compliance Analysis

The PHP prototype contains two Principle 5 violations in `web/templates/partials/document-start.php`:

| Violation | Charter Principle | Fix |
|-----------|-------------------|-----|
| `<script src="https://cdn.tailwindcss.com?plugins=typography">` | Principle 5 — ethical choices must be in the architecture, not a policy promise | Install Tailwind via npm; compile to static CSS via `vite build`. Zero external requests after build. |
| `<link href="https://fonts.googleapis.com/css2?family=Inter...">` | Principle 1 (Google can observe all page loads) + Principle 5 | Replace with `@fontsource/inter` (MIT licensed). Font files bundled into Vite output. No external request possible. |

Both fixes are **architecturally enforced by the build step** — after `vite build`, there is no mechanism to load anything from an external CDN. This satisfies Principle 5: "Ethical choices are in the architecture, not in the terms of service."

---

### 3. Design Tokens

Extracted from the Tailwind config in `document-start.php`:

```js
// tailwind.config.js — extend.colors
ypsiaDark:        '#0f172a'
ypsiaBg:          '#0f172a'   // same as ypsiaDark
ypsiaPanel:       '#1e293b'
ypsiaAccent:      '#6366f1'
ypsiaAccentLight: '#818cf8'

// extend.fontFamily
sans: ['Inter', 'sans-serif']
```

Five color tokens, one font family. Straightforward to port to `tailwind.config.js`.

---

### 4. Charter Rendering — Build-Time Compilation Required

The PHP `ypsia_render_charter_markdown()` function (915-line `bootstrap.php`) implements **section-aware rendering** — the same markdown element is styled differently depending on which `##` section it appears in:

| Element | Section | Behaviour |
|---------|---------|-----------|
| `##` headings | All | `id="slugified-title"` + `class="scroll-mt-28"` — required for sticky nav offset |
| Blockquote | `Doel` | `border-l-4 border-ypsiaAccent bg-ypsiaAccent/5 italic text-lg` |
| Blockquote | `De Noordster` | Same as above + `text-xl font-medium` |
| Blockquote | `Waardepropositie` (belofte sentence) | Same accent style, `font-medium` |
| List items | `Het Probleem` | `<strong>...<br>` (line break after bold prefix, not a space) |
| Table | `Versiegeschiedenis` | `text-sm`, fixed column widths, `font-mono` for version number |

**Decision: Vite plugin for build-time compilation.**

`react-markdown` with plugins cannot cleanly reproduce section-aware rendering without complex, fragile plugin chains. Instead, a dedicated Vite plugin (`vite-plugin-charter`) will:
1. Read `docs/development/CHARTER.md` at build time
2. Apply the identical rendering logic (ported from PHP to JS)
3. Emit a static JSON file: `{ sections: [{id, title}], html: "..." }`
4. The React component imports this JSON — no runtime parser, no external dependency

Benefits:
- Zero runtime dependency (no `react-markdown`, no `marked`, no `remark`)
- Output is deterministic and auditable in git (commit the emitted JSON)
- Rendering logic lives in one place, testable in isolation
- Charter cannot differ from what is in git — no runtime transformation

---

### 5. Routing

Two routes: `/` and `/charter`. Options evaluated:

| Option | Assessment |
|--------|------------|
| `react-router-dom` | Correct tool, minimal footprint, supports `<Link>` for client-side navigation |
| Manual `window.location` / state switch | Avoids a dependency but re-implements routing; not worth it |

**Decision:** `react-router-dom` v7. Nginx requires `try_files $uri /index.html` for SPA fallback.

---

### 6. Waitlist Form — FastAPI Backend

The PHP prototype handled form submission server-side. In a React+Vite SPA there is no server. `backend/` already exists (FastAPI). Form handling belongs in FastAPI — this becomes the **first working API route of the platform**.

Architecture:
- React form submits `POST /api/waitlist` with JSON `{ email, interest }`
- FastAPI validates and stores (SQLite initially)
- Nginx proxies `/api/*` → FastAPI (port 8000)
- Unsubscribe: `DELETE /api/waitlist` with `{ email }`

Honeypot field (`name="website"`, visually hidden, `tabindex="-1"`) is preserved for bot protection — implemented client-side in React, validated server-side in FastAPI.

---

### 7. Umami Analytics

Once Umami is deployed on Hetzner, a single script tag is added to `frontend/index.html`:

```html
<script defer src="https://umami.ypsia.nl/script.js" data-website-id="..."></script>
```

- No cookies set
- No consent banner required (GDPR compliant by design)
- Script domain is owned by Ypsia — no third-party tracking
- Satisfies Principle 1 and Principle 5

Umami deployment is a separate infrastructure task — not in scope for this refactor issue.

---

### 8. Frontend Scaffold State

`frontend/src/App.jsx` contains the default Vite counter demo. No Tailwind, no router, no components. **Complete clean slate** — all `frontend/src/` content will be replaced.

`package.json` currently has:
- `react` 18.3.1
- `react-dom` 18.3.1
- `vite` 5.4.10

Packages to add:
- `tailwindcss` + `@tailwindcss/typography` (build dep)
- `@fontsource/inter` (runtime, bundled)
- `react-router-dom` v7

---

## Decisions

| # | Decision | Rationale |
|---|----------|-----------|
| 1 | Tailwind via npm, not CDN | Charter Principle 5 — architecture enforces it, not policy |
| 2 | `@fontsource/inter` instead of Google Fonts | Charter Principle 1 + 5 — no external observer possible |
| 3 | Build-time charter compilation via Vite plugin | No runtime parser; deterministic; auditable |
| 4 | `react-router-dom` v7 for routing | Standard, minimal, correct tool |
| 5 | Waitlist form → FastAPI `/api/waitlist` | First real backend integration; PHP had no server counterpart |
| 6 | Umami analytics via own domain | Cookieless; no consent banner; charter-compliant |

---

## Out of Scope

- Umami installation on Hetzner (infrastructure issue, separate scope)
- nginx + SSL configuration on Hetzner (infrastructure issue)
- DNS cutover bhosted → Hetzner (operations)
- Any page beyond `/` and `/charter`

---

## Version History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | 2026-03-10 | Agent | Initial scaffold |
| 1.1 | 2026-03-10 | Agent | Full research findings added; translated to English |
