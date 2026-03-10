<!-- docs/planning/issue18/research.md -->
<!-- template=research version=8b7bb3ab created=2026-03-10T17:56Z updated=2026-03-10 -->
# Research: Marketing Site — React+Vite Refactor of PHP Prototype

**Status:** APPROVED
**Version:** 1.2
**Last Updated:** 2026-03-10
**Issue:** [#18](https://github.com/MikeyVK/ypsia/issues/18)
**Parent:** [#17](https://github.com/MikeyVK/ypsia/issues/17)

---

## Problem Statement

The PHP prototype on `feat/17-marketing-site` contains charter violations across two categories:

1. **Principle 5 — CDN dependencies**: `cdn.tailwindcss.com` and `fonts.googleapis.com` are external runtime dependencies. Ethical choices must be in the architecture, not in policy.
2. **Principle 9 + Principle 1 — Waitlist design**: The prototype collects pre-consent health-intent data (`interest` field), has no double opt-in (phishing vector), uses plain CSV storage (tamper-sensitive), and leaks list membership via `?waitlist=already` (enumeration attack).

PHP is also the wrong stack. The existing `frontend/` scaffold (React 18.3.1 + Vite 5.4.10) is the correct foundation. This refactor replaces the PHP approach entirely and anchors charter compliance architecturally.

---

## Research Goals

1. Define exact scope: which pages and features to build, based on PHP prototype
2. Identify all charter violations and their structural fixes
3. Determine design token inventory to preserve visual parity
4. Assess charter rendering complexity and decide on correct rendering strategy
5. Define routing, form handling, and analytics integration approach
6. Design a waitlist system that is architecturally charter-compliant

---

## Findings

### 1. Scope — Exactly Two Pages

| Route | Description |
|-------|-------------|
| `/` | Landing page |
| `/charter` | Charter manifest renderer |

**Landing page sections (in order):**
1. Hero — headline, two-paragraph body copy, feature panel (three items with icons)
2. Charter CTA block — card linking to `/charter`
3. Waitlist section — subscribe form + unsubscribe form, with neutral feedback states

**Charter page:**
- Sticky `manifest` header with TOC toggle button
- TOC flyout panel (left-anchored, backdrop, keyboard dismiss via Escape)
- `<article>` with full charter prose
- Back-to-top button (fixed, bottom-right)

No dashboard, no login, no user state.

---

### 2. Charter Compliance Analysis — CDN Dependencies

Two Principle 5 violations in `web/templates/partials/document-start.php`:

| Violation | Principles | Fix |
|-----------|------------|-----|
| `<script src="https://cdn.tailwindcss.com?plugins=typography">` | Principle 5 | Tailwind via npm; compiled to static CSS by `vite build` |
| `<link href="https://fonts.googleapis.com/css2?family=Inter...">` | Principle 1 + 5 | `@fontsource/inter` (MIT); font files bundled in Vite output |

After `vite build` there is no mechanism to load anything from an external CDN. Architecture enforces it — no policy required.

---

### 3. Charter Compliance Analysis — Waitlist Design

The PHP prototype has four violations:

**Violation A — Pre-consent health-intent data collection (Principle 9)**

The `interest` field asks *"Welke data wil je combineren? (bijv. slaap, Garmin)"* before any terms have been accepted or any account created. This is health-intent data collected in service of Ypsia's product research, not the user. Principle 9: consent is always explicit. There is no consent here.

**Fix:** Remove the `interest` field entirely. Product research happens with existing users who have explicitly consented — never with pre-signup visitors.

**Violation B — No double opt-in (Principle 9 + phishing vector)**

Anyone can submit any email address. The only protection is a honeypot field. Ypsia then sends a mail to someone who never requested it. A form submission is not consent. A confirmation click in the recipient's own inbox is.

**Fix:** Double opt-in required. Status state machine: `pending → confirmed → unsubscribed`. Mail is only sent to `confirmed` addresses.

**Violation C — Plain CSV storage (Principle 5 + Principle 1)**

Email addresses and health-intent data in an unencrypted CSV file. First server compromise = full list immediately readable. This is structurally incompatible with the charter's promise to users.

**Fix:** See §6 — Cryptographic Waitlist Design.

**Violation D — Enumeration via `?waitlist=already` (Principle 1)**

The `already` response state reveals that a specific email address is on the list. This is an enumeration attack: submit addresses systematically, identify who is registered. Privacy-conscious users become identifiable.

**Fix:** Always return an identical response regardless of whether the email was found: *"If this address was on the list, it has been removed."* Server never reveals list membership.

---

### 4. Waitlist — Is It Charter-Compatible?

A classical mailing list — newsletter, drip campaign, re-engagement — is fundamentally incompatible with the charter. It is an engagement mechanism that serves Ypsia, not the user.

A waitlist with the following precise definition is charter-compatible:

> *"You will receive one email on the day this platform becomes available. Nothing before it. Nothing after it."*

This is not data collection for Ypsia's benefit. It is fulfilling a promise to someone who explicitly asked for it.

**Charter compliance of a correctly-defined waitlist:**

| Charter element | Assessment |
|----------------|------------|
| Principle 9 — explicit opt-in | ✅ Double opt-in via confirmation email |
| Principle 9 — always revocable | ✅ Token link in confirmation mail + "lost my mail" unsubscribe flow |
| Anti-Principle engagement | ✅ No newsletter, no drip, no re-engagement — one email, then purge |
| Principle 1 — we cannot read stored data | ✅ Sealed box; private key never on server |
| Principle 5 — in architecture, not policy | ✅ Server structurally cannot decrypt without operator key |
| Principle 7 — transparency | ✅ UI links directly to the GitHub source; code tells the honest story |

**The UI copy:**
> *"You will receive one email on the day we go live. Never before it, never anything else. Want to verify how we enforce this technically? [View the code →](https://github.com/MikeyVK/ypsia)"*

This is Principle 7 as a product feature. The target audience — privacy-conscious people — are exactly the people who will read the code. When they do, they find that abuse is structurally impossible, not promised away. This is the most credible "marketing" possible for Ypsia's mission.

---

### 5. Cryptographic Waitlist Design

**Key architecture: libsodium sealed box + HMAC tokens**

One asymmetric keypair is generated offline by the operator before deployment. The private key is held by the operator only — in a physical vault, YubiKey, or sealed envelope. It never touches the server. The public key is a server environment variable.

**What the server stores per signup:**

| Field | Value | Purpose |
|-------|-------|---------|
| `dedup_hash` | `HMAC(email, DEDUP_SECRET)` | Duplicate detection — not reversible |
| `sealed_email` | `crypto_box_seal(email, PUBLIC_KEY)` | For delivery at launch — unreadable without private key |
| `unsubscribe_token` | `HMAC(email, UNSUB_SECRET)` | In confirmation email — allows unsubscribe without decryption |
| `status` | `pending → confirmed → unsubscribed` | State machine |
| `confirmed_at` | timestamp | Set after confirmation click |

Plaintext email address exists only in server memory during the request. Never written to the database, never written to logs.

**Subscribe flow:**
1. Receive email → compute `dedup_hash` → check for duplicate
2. Identical response whether new or already present (no enumeration)
3. Seal email with public key → store
4. Send confirmation email (plaintext used, then discarded)
5. Status = `pending`

**Confirm flow (click in email):**
- Link contains `?token=<unsubscribe_token>`
- Server verifies token → sets `confirmed_at`, status = `confirmed`
- Same token serves as the unsubscribe token

**Unsubscribe via token (link in confirmation email):**
- `GET /api/waitlist/unsubscribe?token=<token>` → status = `unsubscribed`
- Server never decrypts anything

**Unsubscribe via "lost my email" form:**
1. User enters email address
2. Server computes `HMAC(email, DEDUP_SECRET)`, looks up in DB
3. Regardless of result: response = `"If this address was on the list, it has been removed."`
4. No enumeration possible; server decrypts nothing

**At launch — one-time decryption:**
1. Operator brings private key online via secure channel (for that session only)
2. FastAPI decrypts all `sealed_email` records with status `confirmed` — in memory
3. Batch send launch email
4. Private key discarded — never persisted
5. All `sealed_email` ciphertexts purged — data has served its only purpose

**Result:**
- A Ypsia employee with full database access sees only ciphertexts and hashes — no email addresses
- A server compromise leaks no email addresses — only unreadable sealed boxes
- Unsubscribe always works, even without the original confirmation email
- No enumeration attack possible on any route
- Principle 5: architecturally enforced, not policy-dependent

---

### 6. Design Tokens

Extracted from the PHP Tailwind config:

```js
// tailwind.config.js — extend.colors
ypsiaDark:        '#0f172a'
ypsiaBg:          '#0f172a'
ypsiaPanel:       '#1e293b'
ypsiaAccent:      '#6366f1'
ypsiaAccentLight: '#818cf8'

// extend.fontFamily
sans: ['Inter', 'sans-serif']
```

---

### 7. Charter Rendering — Build-Time Compilation

The PHP `ypsia_render_charter_markdown()` implements section-aware rendering — same markdown element styled differently depending on which `##` section it appears in:

| Element | Section | Behaviour |
|---------|---------|-----------|
| `##` headings | All | `id="slugified-title"` + `scroll-mt-28` (sticky nav offset) |
| Blockquote | `Doel` | Accent border + bg, `italic`, `text-lg` |
| Blockquote | `De Noordster` | Same + `text-xl font-medium` |
| Blockquote | `Waardepropositie` (belofte) | Same accent style, `font-medium` |
| List items | `Het Probleem` | `<strong>...<br>` (line break after bold prefix) |
| Table | `Versiegeschiedenis` | `text-sm`, fixed column widths, `font-mono` for version |

**Decision: Vite plugin for build-time compilation (`vite-plugin-charter`):**
1. Reads `docs/development/CHARTER.md` at build time
2. Applies rendering logic (ported from PHP to JS)
3. Emits static JSON: `{ sections: [{id, title}], html: "..." }`
4. React component imports JSON — zero runtime parser, zero external dependency

---

### 8. Routing

**Decision:** `react-router-dom` v7. Two routes: `/` and `/charter`. Nginx requires `try_files $uri /index.html` for SPA fallback.

---

### 9. Frontend Scaffold State

`frontend/src/App.jsx` is the default Vite counter demo. Complete clean slate — all `frontend/src/` content will be replaced.

Packages to add:
- `tailwindcss` + `@tailwindcss/typography` (build dep)
- `@fontsource/inter` (runtime, bundled by Vite)
- `react-router-dom` v7

---

## Decisions

| # | Decision | Rationale |
|---|----------|-----------|
| 1 | Tailwind via npm, not CDN | Principle 5 — architecture enforces it |
| 2 | `@fontsource/inter` instead of Google Fonts | Principle 1 + 5 — no external observer possible |
| 3 | Build-time charter compilation via Vite plugin | No runtime parser; deterministic; auditable |
| 4 | `react-router-dom` v7 | Correct tool, minimal footprint |
| 5 | Remove `interest` field from waitlist | Principle 9 — pre-consent health-intent data |
| 6 | Double opt-in for waitlist | Principle 9 — form submission is not consent |
| 7 | libsodium sealed box + HMAC tokens | Principle 1 + 5 — server structurally cannot read stored emails |
| 8 | Identical response for all unsubscribe attempts | Principle 1 — no enumeration attack possible |
| 9 | Purge sealed emails after launch send | Data has served its only purpose; no retention beyond need |
| 10 | UI links to GitHub source for waitlist logic | Principle 7 — code tells the honest story; architecture is the proof |
| 11 | Waitlist → FastAPI `/api/waitlist` | First real backend integration; correct separation of concerns |
| 12 | Umami analytics via own domain | Cookieless; no consent banner; Principle 1 + 5 |

---

## Local Development Prerequisites

Required to run the full stack locally. Must be satisfied before the validation phase can execute.

### Layer 1 — Frontend only (pages, no form submission)

| Requirement | Version | Notes |
|-------------|---------|-------|
| Node.js | 20 LTS+ | |
| npm | bundled with Node | |

```bash
cd frontend && npm install && npm run dev
```

### Layer 2 — Full stack (including waitlist API)

| Requirement | Version | Install | Purpose |
|-------------|---------|---------|---------|
| Node.js | 20 LTS+ | nodejs.org | Vite dev server |
| Python | 3.12+ | python.org | FastAPI backend |
| pynacl | latest | `pip install pynacl` | libsodium sealed box encryption |
| fastapi + uvicorn | latest | `pip install fastapi uvicorn` | API server |
| SQLite | bundled with Python | — | Waitlist storage; no install needed |
| Mailpit | latest | mailpit.axllent.org | Local SMTP catcher — captures outgoing mails in web UI at `localhost:8025`; required to test double opt-in and unsubscribe flows |

### Keypair generation (one-time, dev environment)

No extra tooling required — `pynacl` provides everything. A CLI script `scripts/generate_keypair.py` will be provided that generates a dev keypair and writes to `.env.local`. The production keypair is generated offline on the operator's own machine — never on the server.

### Validation gate

The validation phase requires all Layer 2 prerequisites to be present. The following flows must be testable end-to-end before the phase passes:

1. Subscribe → receive confirmation email in Mailpit → click confirm link → status = `confirmed`
2. Unsubscribe via token link → status = `unsubscribed`
3. Unsubscribe via "lost my email" form → identical response regardless of whether address exists
4. Bot submission (honeypot filled) → silently rejected, no mail sent
5. Duplicate submission → identical response to new submission (no enumeration)
6. Launch decryption script → all `confirmed` sealed emails decryptable with dev private key

> **Note for environment setup:** A separate agent will prepare the local development environment (Node.js, Python, Mailpit, keypair generation) so this agent's context remains focused on the refactor implementation.

---

## Out of Scope

- Umami installation on Hetzner (infrastructure, separate issue)
- nginx + SSL configuration on Hetzner (infrastructure)
- DNS cutover bhosted → Hetzner (operations)
- Any page beyond `/` and `/charter`
- Local environment setup (handled by separate agent before validation phase)

---

## Version History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | 2026-03-10 | Agent | Initial scaffold |
| 1.1 | 2026-03-10 | Agent | Full research findings; translated to English |
| 1.2 | 2026-03-10 | Agent | Waitlist violations identified; cryptographic design; charter compatibility analysis; GitHub transparency link |
| 1.3 | 2026-03-10 | Agent | Local development prerequisites; validation gate definition; environment setup delegated to separate agent |
