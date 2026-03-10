<!-- docs\planning\issue18\research.md -->
<!-- template=research version=8b7bb3ab created=2026-03-10T17:56Z updated= -->
# Marketing site: React+Vite refactor van PHP prototype

**Status:** DRAFT  
**Version:** 1.0  
**Last Updated:** 2026-03-10

---

## Problem Statement

Het PHP prototype op feat/17-marketing-site bevat twee charter-schendingen (Principe 5): cdn.tailwindcss.com en fonts.googleapis.com. Bovendien is PHP de verkeerde stack — de bestaande frontend/ scaffold (React 18.3.1 + Vite 5.4.10) is het juiste fundament. De refactor vervangt de PHP aanpak volledig en verankert charter-compliance architecturaal in de buildstap.

## Research Goals

- Twee pagina routes bouwen: / (landing) en /charter (manifest) — identiek aan PHP prototype in functionaliteit en opmaak
- Charter-compliance architecturaal verankeren: Tailwind via npm+Vite, @fontsource/inter (MIT) — na Vite build nul externe requests
- CHARTER.md build-time compileren via Vite plugin naar statisch HTML/JSON artefact — geen runtime markdown parser
- Waitlist form koppelen aan FastAPI backend via /api/waitlist endpoint (eerste werkende API route van het platform)
- Umami analytics integreren via eigen script-tag in index.html (cookieloos, geen consent-banner, domein van henzelf)

## Related Documentation
None
---

## Version History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 |  | Agent | Initial draft |