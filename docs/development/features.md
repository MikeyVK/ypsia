<!-- docs\development\features.md -->
<!-- template=research version=8b7bb3ab created=2026-03-07T15:58Z updated=2026-03-07 -->
# Feature Backlog

> **Werknaam:** Project Sovereign · BaseLine · Vita *(definitieve naam volgt na manifest)*
> **Vorige werknaam:** AthleteCanvas — te smal voor de bredere doelgroep; wordt niet meer gebruikt als productnaam in dit document.

**Status:** DRAFT | **Version:** 4.0 | **Last Updated:** 2026-03-08

---

## Kernidentiteit

Dit is geen sportenapp. De bewuste mens staat centraal — iemand die regie wil over zijn biologische en cognitieve bandbreedte, ongeacht of hij sporter, kenniswerker, maker, ouder, of chronisch ziek is.

Het eerste product-vertrekpunt is de serieuze sporter (omdat dat de eerste gebruiker is en de rijkste databron), maar sport is een *toegangspoort*, geen *eindbestemming*. Elk feature, elk cluster wordt getoetst aan de vraag: past dit ook voor wie niet sport?

De filosofie Is het product. Technologie is de verpakking.

---

## How to Use This Document

Living product discovery document. What this platform should **do** — from the user's perspective, not an engineer's. No architecture, no implementation scope, no sprint references.

**Flags:** `⚠️` arch-review needed · `💭` brainfart/unvalidated · `R:` Requires another feature first

**Feature entry format:** one row per feature — name, one-sentence description, flags. Detail and elaboration belong elsewhere.

**Contributing rules:**
1. No architecture, tech choices, or implementation approach.
2. No sprint or epic references.
3. User perspective only: "The user can…"
4. Brainfarts welcome — use `💭` if raw/unvalidated.
5. Flag architectural implications with `⚠️` — review happens in `docs/planning/issue16/research.md`.

---

## Cluster Index

| # | Cluster | Description |
|---|---------|-------------|
| 1 | [Data Connectivity](#cluster-1--data-connectivity) | Garmin, Strava, Polar, Fitbit, Withings, Apple Health, manual sync, externe contextdata |
| 2 | [Personal Dashboard & Profile](#cluster-2--personal-dashboard--profile) | **2A** Terugkijken · **2B** Het heden (bruglaag) · **2C** Vooruitkijken |
| 3 | [Activity Detail & Analysis](#cluster-3--activity-detail--analysis) | Kaart, grafieken, gear tracking, activity compare |
| 4 | [AI & Insights](#cluster-4--ai--insights) | Semantisch zoeken, patroondetectie, plan-vergelijking, anomaliedetectie |
| 5 | [Social & Community](#cluster-5--social--community) | Schone social feed, profiel/identiteit, creator platform, externe socials, expliciete anti-features filosofie |
| 6 | [Health & Wellbeing](#cluster-6--health--wellbeing) | Slaap, HRV, stress, gewicht, voeding, supplementen, revalidatie |
| 7 | [Mobile](#cluster-7--mobile) | Offline, widgets, push notifications, app activation sync |
| 8 | [Data Ownership & Privacy](#cluster-8--data-ownership--privacy) | Export, deletie, consent management |
| 9 | [Planning & Goals](#cluster-9--planning--goals) | Trainingsplanning, periodisering, event kalender, SMART-doelen |
| 10 | [Kookboek & Boodschappen](#cluster-10--kookboek--boodschappen) | Recepten, maaltijdplanner, boodschappenlijst, supermarkt-integratie |
| 11 | [Coach & Professional Portal](#cluster-11--coach--professional-portal) | B2B coach portaal, fysiotherapeut view, multi-atleet beheer |
| 12 | [Monetisatie & Partnerships](#cluster-12--monetisatie--partnerships) | Subscription model, affiliate/partner programma, early adopter strategie |
| 13 | [Journal & Reflection](#cluster-13--journal--reflection) | Data-aware journaling, AI journaalpartner, habit tracker, cognitieve performance, journal mode |
| 14 | [Informatie- & Aandachtsdieet](#cluster-14--informatie---aandachtsdieet) | Cognitive load tracking, doomscroll-correlator, informatie macro's, flow-detectie |
| 15 | [Digitale Soevereiniteit & Offline Beloningen](#cluster-15--digitale-soevereiniteit--offline-beloningen) | Slaapkamer-slot, unplugged credits, local-first processing, partner rewards |
| 16 | [Life Capacity & Biologische Ritmes](#cluster-16--life-capacity--biologische-ritmes) | Life Battery, biologische seizoenen, work vs. recovery balance |
| 17 | [Persoonlijke Waarheidsvinding](#cluster-17--persoonlijke-waarheidsvinding-truth-engine) | Symptoom-leugendetector, placebo tracker, omgevingsimpact, what-if simulator |
| 18 | [Platform Architectuur & Ethiek](#cluster-18--platform-architectuur--ethiek) | Open core, zero-engagement codebase, cryptografische deletie, portable identity |
| 19 | [Nieuwe Doelgroepen](#cluster-19--nieuwe-doelgroepen) | Neurodivergent, chronisch ziek, creatieve makers, survival ouders |
| 20 | [Anti-Viral Social Sanctuary](#cluster-20--anti-viral-social-sanctuary) | Zero-discovery, context-first publishing, creator-patron model, studio profiel |

---

### Cluster 1 — Data Connectivity
*The user connects AthleteCanvas to the places where their life is already being tracked.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Garmin: full history import | Eenmalige import van de volledige Garmin-geschiedenis bij eerste koppeling. | ⚠️ |
| Garmin: automatic background sync | Garmin-data blijft automatisch bijgewerkt — nooit handmatig hoeven syncen. | |
| Garmin: complete data scope | Import omvat activities, sleep, Body Battery, stress, daily summaries, body metrics, VO2max, training status/load, recovery time, HR zones en PRs. | ⚠️ |
| Strava: automatic activity sync | Nieuwe Strava-activiteiten verschijnen automatisch inclusief GPS, HR, power, segmenttijden en gear; history import incluis. | |
| Polar: automatic sync | Activities, sleep, daily summaries, recovery score, nightly recharge en orthostatic test resultaten automatisch geïmporteerd. | |
| Fitbit: automatic sync | Activities, sleep (stages, score, duration), HR en body metrics (Aria scale) automatisch geïmporteerd. | |
| Withings: automatic sync | Weegschaal, slaapmat, bloeddrukmonitor en thermometer-data automatisch geïmporteerd. | |
| Wahoo: automatic activity sync | Cycling en running activiteiten van Wahoo ELEMNT volledig geïmporteerd. | |
| Oura Ring: automatic sync | Sleep score, readiness, HRV, activiteit, lichaamstemperatuur en ademhalingsfrequentie automatisch geïmporteerd. | 💭 |
| Whoop: automatic sync | Strain, recovery, sleep, HRV en resting HR automatisch geïmporteerd. | 💭 |
| Apple Health: sync on iOS | Leest alle Health-data die de gebruiker heeft toegestaan: workouts, sleep, HR, HRV, gewicht, voeding en meer. | ⚠️ |
| Google Health Connect: sync on Android | Leest alle Health Connect-data die de gebruiker heeft toegestaan. | ⚠️ |
| Manual activity entry | Activiteit handmatig loggen zonder apparaat: type, datum, duur, optioneel afstand/HR/RPE/notities. | |
| Manual body metrics entry | Gewicht, vetpercentage of andere metingen handmatig invoeren. | |
| FIT / GPX / TCX file import | Bestand direct uploaden en importeren als activiteit. | |
| Sync status dashboard | Status van elke gekoppelde bron in één oogopslag: laatste sync, actief/inactief, fouten. | |
| Manual sync trigger | Directe sync forceren per bron zonder op de automatische cyclus te wachten. | |
| Activation-triggered sync | Bij het openen van de app wordt automatisch een sync gestart. | |
| Source disconnection & re-auth | Bron ontkoppelen verwijdert geen data; verlopen auth triggert notificatie. | |
| Sync history log | Log van recente sync-events per bron: tijdstip, aantal records, fouten. | |

**Externe contextdata** — geen gebruikersapparaat, maar vrij beschikbare omgevings- en referentiedata die de app automatisch koppelt aan activiteiten, hersteldata en planning.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Luchtkwaliteit (AQI) | Fijnstof (PM2.5/PM10), stikstofdioxide en ozon op tijdstip en locatie van elke activiteit; verklaart waarom een duurloop zwaarder voelde dan de data suggereert. | |
| Pollenconcentraties | Regionale pollenkaart (grassen, bomen, kruiden) automatisch gekoppeld aan activiteiten en hersteldata; bruikbaar voor correlatie met allergiesymptomen. | |
| Uitgebreide weersdata | Naast basisweer: hittestress (WBGT), windchill, gevoelstemperatuur, UV-index, barometrische druk, dauwpunt, zichtbaarheid — allemaal historisch en als voorspelling. | |
| Zonsopkomst & daglichtduur | Dagelijkse zonsopkomst/-ondergang en totale daglichtduur per locatie; context voor circadiane ritme, winterdip en trainingstiming. | |
| Barometrische druktrend | Dalende luchtdruk correleert met gewrichtsklachten; relevante context bij revalidatie en onverklaard mindere prestatie. | 💭 |
| KNMI waarschuwingen | Officiële hitte-, storm- en gladheidswaarschuwingen (code oranje/rood) als context bij geplande buitentrainingen. | |
| RIVM griep- & virusradar | Regionaal griep- en virusniveau (RIVM opendata); verklaart prestatiedip of trager herstel zonder aanwijsbare trainingsoorzaak. | 💭 |
| Open Food Facts | Koppeling aan vrije wereldwijde voedingsdatabank voor barcode-scan en macrozoekfunctie in voedingslogging. | R: Nutrition logging |
| NEVO voedingsstoffendatabank | Koppeling aan officiële Nederlandse voedingsstoffendatabank (RIVM) als referentie voor macro- en micronutriënten. | R: Nutrition logging |
| Agenda-integratie | Opt-in koppeling met Google Calendar of Outlook; drukke periodes en reisdagen als life-stress-context voor AI-analyse. | 💭 ⚠️ |
| Tijdzone & jet lag context | Reisdetectie op basis van locatiewijziging; automatische jet lag-context bij prestatie- of slaapafwijkingen na reizen. | 💭 |
| OpenStreetMap terreindata | Ondergrond van route (asfalt, grind, trail, zand) en bebouwingsdichtheid automatisch bepaald vanuit GPS-track; context voor tempo- en belastingsvergelijking. | 💭 |
| Schermtijd-integratie | Opt-in koppeling met iOS Screen Time of Android Digital Wellbeing; late schermtijd als verklarende factor bij slaapkwaliteit. | 💭 ⚠️ |
| Race- & eventendatabank | Koppeling aan openbare eventdatabases (World Athletics, Let's Do This) voor automatisch importeren van races in de evenementenkalender. | 💭 R: Evenementenkalender |
| Geomagnetische activiteit (Kp-index) | Dagelijkse geomagnetische index (NOAA opendata) als experimentele correlatiefactor voor HRV-afwijkingen. | 💭 |
| Maanfase | Maanfase als optionele correlatiefactor voor gebruikers die dit willen tracken; geen claims, puur datapunt. | 💭 |

---

### Cluster 2 — Personal Dashboard & Profile
*The user's home base. Verleden, heden en toekomst in één vloeiend geheel — zonder dat het geforceerd voelt.*

**2A — Terugkijken** — wat was er, hoe ging het, wat bouwde ik op.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Activity feed | Chronologische lijst van recente activiteiten van alle gekoppelde bronnen. | |
| Monthly and yearly summary | Maand- en jaartotalen met vergelijking naar dezelfde periode vorig jaar. | |
| Training load overview | Visuele trend van trainingsbelasting: opbouwend, stabiel of taperend; duurzaamheidsindicator. | |
| Body metrics section | Gewicht, vetpercentage-trend, VO2max-trend en resting HR-trend in simpele grafieken. | |
| Activity calendar | Maandkalender met actieve dagen gemarkeerd; klik op dag toont alle activiteiten. | |
| Yearly training heatmap | Volledig jaar als heatmap (GitHub-stijl): kleurintensiteit = trainingsvolume per dag. | |
| Training streaks | Huidige en langste streak van opeenvolgende actieve dagen; definitie van "actief" instelbaar. | |
| Achievements and milestones | Automatische detectie en viering van mijlpalen: eerste 100km-maand, langste rit, snelste 5K, etc. | |

**2B — Het heden** — de bruglaag: historische patronen ontmoeten de dag van vandaag.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Today at a glance | Samenvattingskaart: stappen, calorieën, resting HR, Body Battery, stress, slaap, activiteit vandaag. | |
| Recovery status card | Prominente herstelstatus in begrijpelijke taal, op basis van slaap, HRV, HR en load. | R: sleep, HRV, training load |
| This week summary | Weekoverzicht: trainingsuren, afstand per sport, trainingsbelasting deze vs. vorige week. | |
| Huidige AQI & pollen voor locatie | Actuele luchtkwaliteit en pollenstand vóórdat de gebruiker besluit naar buiten te gaan. | R: Luchtkwaliteit (AQI), Pollenconcentraties |
| Vandaag weersvenster | "Beste trainingsmoment vandaag: 17:00–19:00, daarna regen" — op basis van uurvoorspelling. | R: Uitgebreide weersdata |
| HRV-context voor geplande sessie | Koppeling van huidig HRV aan historische sessies: "Je beste intervalsessies waren op dagen met HRV boven jouw norm. Vandaag: ruim boven norm." | R: HRV tracking, Trainingsplan generator |
| Plan vs. lichaam conflict | Wanneer het plan een zware sessie vraagt maar hersteldata iets anders suggereert, biedt de app drie opties: doorgaan, aanpassen of verschuiven — zonder te oordelen. | R: Recovery score, Trainingsplan generator |
| Sleep debt tracker | Cumulatief slaaptekort over 7 en 14 dagen als aanhoudende context — niet alleen gisternacht. | R: Sleep dashboard |
| Stressbudget | Trainingsbelasting + life stress (agenda) gecombineerd tot één totaalbelasting; "je bent vol"-signaal. | 💭 R: Agenda-integratie |
| Cafeïne-timing | Op basis van trainingstijd en gewenste slaaptijd: "laatste koffie uiterlijk 14:30 voor optimale slaap vanavond." | 💭 |
| Hydratatiestatus vandaag | Geschatte vochtbehoefte op basis van gisteren's intake, vandaag's training en actueel weer. | R: Hydration logging, Uitgebreide weersdata |
| Gear ready check | Vermelding wanneer primaire gear de vervangingsdrempel nadert vóór een geplande lange sessie. | R: Gear tracking, Evenementenkalender |
| Next action card | Één ding: de belangrijkste actie van vandaag voor je doelen — training, slaap, voeding of rust. | 💭 |
| One-tap logging bar | Stemming, energie, water, supplement direct vanuit home screen loggen — geen schermwissel. | 💭 |
| Habitcontinuïteit | Subtiele markering wanneer een gewoonte-patroon dreigt te breken: "Je traint 14 weken op rij op dinsdag — morgen is het zo ver." | 💭 |
| Alcohol impact tracker | Opt-in; koppelt alcoholinname aan volgende-dag HRV-afwijking — puur informatief, geen moraal. | 💭 ⚠️ |
| Seizoensvergelijking | "Zelfde week vorig jaar: opbouwfase week 4. Nu week 6 — je bent verder in je seizoen dan een jaar geleden." | |

**2C — Vooruitkijken** — wat staat er te gebeuren, wat vraagt dat van mij.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Vandaag-preview | Geplande sessie vandaag: type, duur, doelzones en focus — eerste wat de gebruiker ziet bij openen. | R: Trainingsplan generator |
| Week-ahead view | Gecombineerd overzicht komende 7 dagen: training, maaltijden en rustdagen als één geïntegreerde tijdlijn. | R: Trainingsplan generator, Maaltijdplanner |
| Komende week macro-preview | Dagelijkse macro-doelen voor de komende week op basis van trainingsbezetting: zware dag = meer koolhydraten. | R: Maaltijdplanner, Trainingsplan generator |
| Morgen-voorbereiding | Samenvatting van morgen: sessie, geplande maaltijden, aanbevolen slaaptijdstip. | R: Trainingsplan generator |
| Rustdag-aanbeveling | AI-gestuurde suggestie voor wanneer de volgende rustdag moet vallen op basis van load en hersteltrend. | R: Recovery score |
| Load forecast | Predictieve load-curve: als ik mijn plan volg, wat is mijn form (TSB) op racedag? | R: Trainingsplan generator, Evenementenkalender |
| Periodisering-fase indicator | Compacte aanduiding in welke fase de gebruiker vandaag zit: opbouw, piek, taper of herstel. | R: Periodisering |
| Weersimpact vooruitkijk | Weersvoorspelling voor geplande buitensessies komende 3–5 dagen; luchtkwaliteit en pollen incluis. | R: Uitgebreide weersdata, Trainingsplan generator |
| Evenement-countdown | Prominente teller naar het volgende A-event met huidige planningsfase erbij. | R: Evenementenkalender |
| Slaapadvies vanavond | "Morgen zwaar interval — streef naar 8u slaap, ga om 22:30 naar bed." | R: Trainingsplan generator |
| Nutriëntentekort-voorspelling | Op basis van komende trainingsweek en huidig voedingsplan: "je haalt je eiwitdoel waarschijnlijk niet op woensdag." | 💭 R: Maaltijdplanner, Trainingsplan generator |
| Seizoensoverzicht vooruit | Resterende events + trainingsblokken voor het lopende seizoen in één oogopslag. | R: Evenementenkalender, Periodisering |
| Persoonlijk piekvenster | "Historisch presteer jij het best 18–22 dagen na je hoogste trainingsweek — dat valt op [datum]." | 💭 R: Long-term trend analysis |
| Energie-beschikbaarheid (RED-S risico) | Calorieën in vs. trainingsuitgave over de week; signalering bij structureel onderfuelen. | 💭 ⚠️ R: Nutrition logging |

**Profiel & configuratie**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Configurable home dashboard | Widgets vrij te rangschikken naar eigen voorkeur, met slimme defaults. | |
| User profile | Persoonsgegevens, fysieke kenmerken, trainingshistorie, gezondheidcontext (optioneel, gevoelig, explicit consent). | ⚠️ |
| HR zone configuration | HR-zones handmatig instellen of automatisch berekenen vanuit max HR of lactaatdrempel; meerdere schemas. | |
| FTP and threshold configuration | FTP en/of drempelpace instellen voor berekening van trainingsbelasting en zone-effort. | |
| Goal setting | Persoonlijke doelen instellen: afstand, racedoel, gewicht, sessiefrequentie; zichtbaar als voortgang-widgets. | |
| Gear / equipment tracking | Gear toevoegen, koppelen aan activiteiten, kilometerstand bijhouden, vervangingsalert. | |

---

### Cluster 3 — Activity Detail & Analysis
*Every workout tells a story. AthleteCanvas makes it easy to read.*

**Basisgrafieken & kaart**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Interactive map | Route op interactieve kaart; hover toont bijbehorend datapunt op elk moment. | |
| Elevation profile | Hoogteprofiel met totale stijging en daling. | |
| HR graph with zone overlay | Hartslagcurve ingekleurd per zone; time-in-zone samengevat. | |
| Pace / speed graph | Tempo of snelheid over tijd, optioneel gecombineerd met hoogteprofiel. | |
| Power graph | Vermogenscurve met zones, normalised power, intensity factor en TSS. | |
| Cadence graph | Cadans (spm of rpm) over tijd. | |
| All-metric timeline | Gecombineerde tijdlijn waarop de gebruiker zelf kiest welke metrics worden overlayd. | |
| Lap table | Alle rondes in een tabel: afstand, tijd, tempo/power, HR; klik markeert op kaart en grafieken. | |
| Best efforts within activity | Automatisch gedetecteerde best efforts op standaard afstanden; PR-markering indien van toepassing. | |
| Race predictions from activity | Racetijdvoorspellingen op basis van de huidige activiteit. | |
| Peak power curve (MMP) | Mean Maximal Power curve: beste gemiddeld vermogen over 1s, 5s, 1min, 5min, 20min, 60min — vergeleken met eigen all-time MMP. | |

**Biomechanische diepgang**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Running dynamics | Ground contact time, vertical oscillation, stride length en vertical ratio per km getrend — niet alleen als sessiegemiddelde. | |
| Loopvorm-vermoeidheidsdetectie | Automatische detectie wanneer cadans daalt, stap korter wordt en vertical oscillation toeneemt; markeert het omslagpunt in de activiteit. | |
| Links/rechts asymmetrie | Ground contact balance (lopen) en power balance (fietsen) over tijd; afwijkingen boven drempel historisch getrackt als blessurepreventiesignaal. | |
| Grade Adjusted Pace (GAP) | Hoogtegecorrigeerd tempo per segment — vergelijkt hellende routes eerlijk en toont de ware inspanning ongeacht het hoogteprofiel. | |
| Cardiac drift / aerobic decoupling | Mate van HR-stijging bij gelijkblijvend tempo als maat voor aerobe efficiëntie; efficiency factor getrackt over activiteiten. | |
| Power-to-HR ratio per segment | Vermogen per hartslag per segment — daalt dit binnen een sessie: vermoeidheid; stijgt dit over weken: fitnesswinst. | |
| HR-herstel na inspanningssegmenten | Hoe snel daalt HR na een interval (HRR); getrackt per activiteit als fitnessmarker over tijd. | |
| SPO2-daling bij hoogte | Zuurstofverzadigingsdip tijdens altitude-activiteiten; relevant voor berglopers en hoogtestages. | |
| Ademhalingsfrequentie-trend | Ademhaling per minuut over de sessie als extra vermoeidheids- en inspanningsindicator, indien beschikbaar via apparaat. | |

**Kaart- en route-intelligentie**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Effort intensity heatmap op route | Route ingekleurd op persoonlijke effort: hoe presteerde ik hier vs. mijn eigen historisch gemiddelde op dit stuk — niet op absoluut tempo. | |
| Windrichting overlay | Windrichting en -kracht per routesegment; verklaart asymmetrie tussen heen en terugsegmenten. | R: Uitgebreide weersdata |
| Terrein/oppervlakte overlay | Asfalt, gravel, trail of zand per metersegment via OpenStreetMap; context voor tempo- en blessurelast. | R: OpenStreetMap terreindata |
| Routemoeilijkheidscore | Hoogte + oppervlak + afstand gecombineerd tot één vergelijkingsgetal; maakt een heuvelachtige 8K eerlijk vergelijkbaar met een vlakke 10K. | 💭 |
| Activiteit-animatie replay | Bewegende stip over de kaart gesynchroniseerd met alle grafieken; deelbaar als GIF of korte video. | 💭 |
| Best versie van deze route | Welke datum en omstandigheden leverden het beste resultaat op dit circuit — context voor zowel training als racestrategie. | |
| Route-bookmark & planning | Routes opslaan als favoriet; nieuwe route tekenen op de kaart vóórdat je gaat trainen. | 💭 |
| Ghost-vergelijking op kaart | Jouw beste prestatie op dezelfde route als bewegende ghost naast de huidige activiteit. | 💭 |

**Fysiologische signalen**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Splitstorategie-analyse | Even split, negative split of positive split automatisch gedetecteerd; historisch patroon: "je begint consistent te snel op 10K." | |
| Afwijkende HR-patronen | Onverwachte piek, onregelmatigheid of anomalie binnen de sessie gemarkeerd — niet als medisch alarm maar als "dit was ongebruikelijk, bekijk het." | 💭 |

**Contextuele intelligentie**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| "Waarom was dit zwaarder dan normaal?" | Automatische multi-factor uitleg op basis van weer, luchtkwaliteit, gear-leeftijd, loadstatus en slaap: "Je liep 8% langzamer maar HR was normaal — waarschijnlijk 29°C + 70% vochtigheid." | ⚠️ R: Uitgebreide weersdata, Luchtkwaliteit (AQI) |
| Activiteit in weekcontext | Deze sessie gepositioneerd in de lopende week: al gedaan volume, resterende ruimte, bijdrage aan weekdoel. | R: Trainingsplan generator |
| Activiteit in trainingsblok | "Dit was jouw 4e kwaliteitssessie van 5 in week 6 van 12 — past bij de verwachte belasting in deze fase." | R: Periodisering |
| Cumulatieve vermoeidheid op dit moment | Wat was de TSB/form op het moment van deze activiteit — achteraf verklarend waarom een sessie goed of slecht voelde. | R: Training load analysis |
| Gear-context per activiteit | Welke schoen of fiets werd gebruikt, hoeveel km oud, en wat was het effect op cadans of tempo t.o.v. dezelfde route met andere gear. | 💭 R: Gear tracking |
| Muziek vs. prestatie | Opt-in; welke muziek draaide er en wat deed dat met HR en cadans — "cadans synchroniseerde aantoonbaar met BPM van je hardste tracks." | 💭 ⚠️ |
| Training effect positionering | Niet alleen "aerobe TE 3.2" maar: "dit was het type prikkel dat je vorige maand ontbrak in je trainingsblok." | R: Periodisering, Methodiek-bibliotheek |

**Narratief & storytelling**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Auto-generated activity story | AI schrijft een korte persoonlijke beschrijving van de activiteit in gewone taal; bewerkbaar en deelbaar. | 💭 |
| Moment of the activity | Automatisch gedetecteerd hoogtepunt van de sessie: PR-moment, pieksinspanning of bijzondere locatie — één ding om te onthouden. | 💭 |
| Shareable activity card | Visueel samenvattingskaartje met kaart, key stats en weerscontext; klaar om te delen (opt-in). | 💭 |
| Activity certificate | Voor races of bijzondere prestaties: opgemaakt bewijs met tijd, route en key stats. | 💭 |

**Post-activiteit flow**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Smart post-activiteit checklist | Direct na import contextuele prompts: log gewicht, beoordeel inspanning, voeg herstelnoot toe — afgestemd op het type sessie. | 💭 |
| Recovery window alert | "Je hebt 30 minuten om te eten voor optimaal spierherstel — hier is een suggestie op basis van sessie-intensiteit en jouw gewicht." | 💭 R: Nutrition logging |
| Blessurerisico-signalering | Loopvorm-afwijkingen die historisch correleerden met een blessure worden gemarkeerd bij herhaling: "dit patroon zagen we voor je shinblessure in maart." | 💭 ⚠️ |
| Volgende sessie preview | Direct na afsluiten activiteit: wat staat er morgen op het plan, en is dat realistisch gezien hoe deze sessie verliep? | R: Trainingsplan generator, Recovery score |

**Beheer**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Activity effort rating (RPE) | Gebruiker voegt een subjectieve inspanningsscore (1–10) toe aan elke activiteit. | |
| Activity notes | Vrije-tekst notities per activiteit, doorzoekbaar. | |
| Activity tags | Gebruikersgedefinieerde tags (bijv. "race", "fasted"); filterbaar in de activiteitenlijst. | |
| Photo attachments | Foto's toevoegen aan een activiteit. | |
| Export activity | Activiteit exporteren als .gpx, .fit of .tcx. | |
| Edit activity | Titel, type, gear, beschrijving en tags bewerken van elke activiteit. | |
| Activity comparison | Twee activiteiten naast elkaar: zelfde kaart, uitgelijnde grafieken, statentabel. | |
| Similar activities | Automatisch gesuggereerde vergelijkbare activiteiten op dezelfde route of met vergelijkbare inspanning. | ⚠️ |
| Segment analysis | Automatisch gedetecteerde terugkerende segmenten met historische prestatietrend. | 💭 |

---

### Cluster 4 — AI & Insights
*Every other app shows you your data. AthleteCanvas understands it.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Natural language search | Zoeken in activiteiten met gewone taal: "lange duurlopen in de regen", "ritten na een slechte nacht". | |
| Conversational data exploration | Gesprek met eigen data: "Hoe heeft mijn weekkilometrage zich dit jaar ontwikkeld?" — antwoord in gewone taal met grafieken. | |
| AI coach weekly summary | Wekelijkse gepersonaliseerde samenvatting: wat gedaan, hoe het lichaam reageerde, aandachtspunten voor komende week. | |
| Pattern detection | Automatisch gevonden patronen: "Je presteert 8–12% slechter na minder dan 7u slaap." Informerend, niet lerend. | |
| Training load analysis (ATL/CTL/TSB) | Acute load, chronic load en form (TSB) over tijd met uitleg en overreaching-signalering. | |
| Recovery score | Dagelijkse herstelscore samengesteld uit slaap, HRV, resting HR, trainingsload en stress; uitgelegd in gewone taal. | |
| Anomaly detection | Proactieve signalering van afwijkingen: HR-piek op easy run, gewichtssprong, dalende slaapkwaliteit, stijgende resting HR. | |
| Personal bests (PB) tracking | Automatische detectie en viering van PRs over alle metrics en afstanden; volledig doorzoekbare PB-geschiedenis. | |
| Performance predictions | Racetijdvoorspellingen en FTP-schatting op basis van trainingshistorie. | 💭 |
| Training readiness score | Dagelijkse score: klaar voor een zware sessie, rustig aan, of rust nodig — met uitleg. | |
| Long-term trend analysis | Fitness- en gezondheidstrends over maanden en jaren: VO2max, resting HR, gewicht, slaap, volume. | |
| Nutrition & performance correlation | Correlaties tussen voeding en prestatie/herstel, indien de gebruiker voeding logt. | 💭 ⚠️ |
| "What if" scenario exploration | Hypothetische vragen: "Wat gebeurt met mijn form als ik twee weken rust neem?" | 💭 |
| Trainingsplan response profiling | Op basis van historische data bepaalt de app hoe de gebruiker reageert op volume vs. intensiteit: "Jouw VO2max groeit sterker bij meer duurvolume dan bij intensiteitspieken." | 💭 ⚠️ |
| Trainingsplan vergelijking (A vs. B) | Twee plannen naast elkaar geanalyseerd op basis van historische respons: welk plan past het best bij het huidige fitnessniveau en de doelstelling — inclusief signalering wanneer een stijlwisseling juist de betere prikkel is. | 💭 ⚠️ R: Trainingsplan generator, Trainingsplan response profiling |
| Trainingsprikkel-vernieuwing signalering | De app detecteert wanneer de gebruiker meerdere seizoenen dezelfde methodiek volgt en de aanpassingscurve afvlakt; suggereert een alternatieve methodiek als nieuwe stimulus. | 💭 R: Methodiek-bibliotheek, Long-term trend analysis |

---

### Cluster 5 — Social & Community
*Een schone sociale laag — gebouwd op wederzijds respect, expliciete toestemming en geestelijke gezondheid als ontwerpprincipe.*

---

> **Platformfilosofie — de anti-features**
>
> AthleteCanvas kiest bewust tegen de mechanismen die sociale media schadelijk maken. Dit zijn geen technische beperkingen — het zijn ontwerpprincipen die in het DNA van het platform verankerd zijn.
>
> - **Geen infinity scroll.** De feed heeft een dagelijks eindpunt. "Je hebt alles gezien voor vandaag" is een feature, geen fout.
> - **Geen algoritmische rangschikking.** De tijdlijn is chronologisch, altijd, zonder uitzondering. Er is geen verborgen mechanisme dat bepaalt wat je ziet.
> - **Geen engagement-optimalisatie.** AthleteCanvas optimaliseert niet op clicks, tijd-in-app of reacties. Die metrics bestaan intern niet als doel.
> - **Geen publieke like-tellers.** Appreciatie is zichtbaar voor de poster, nooit voor derden. Status wordt niet gemeten in aantallen.
> - **Geen aanbevelingsalgoritme.** De app raadt geen content of mensen aan op basis van gedragsdata. Ontdekking is handmatig en intentioneel.
> - **Geen advertenties in de feed.** Commerciële partnerships (zie Cluster 12) zijn transparant en buiten de sociale feed.
> - **Geen notificatie-farming.** Notificaties voor sociale interacties zijn standaard uit; de gebruiker zet ze bewust aan.
> - **Geen dark patterns.** Geen "je hebt 3 meldingen" badges die ongelezen acties fabriceren. Geen re-engagement e-mails met valse urgentie.
> - **Geestelijke gezondheid is fysieke gezondheid.** Dopamine-manipulatie via sociale mechanismen staat haaks op de kernmissie van de app. Wat schadelijk is voor je geest past niet in AthleteCanvas.
> - **Data wordt niet verhandeld.** Sociale gedragsdata (wat je leest, hoe lang, wat je liket) wordt nooit gebruikt voor targeting, verkoop of profilering.

---

**5A — Collaboratieve health features**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Training partner | Trainingspartner aanwijzen wiens activiteiten zichtbaar zijn — alleen na wederzijdse opt-in; te allen tijde intrekbaar. | ⚠️ |
| Athlete compare | Side-by-side vergelijking met een partner: volume, load-trend, tempo, slaap — na expliciete wederzijdse toestemming. | ⚠️ R: Training partner |
| Train together | Activiteiten van een gezamenlijke sessie koppelen en naast elkaar analyseren; toekomstig: live-modus. | 💭 ⚠️ |
| Community benchmarks | Geanonimiseerde vergelijking met vergelijkbare gebruikers — geen individuele data zichtbaar, alleen statistische distributies. | ⚠️ |
| Group challenges | Uitdagingen aanmaken of joinen met leaderboard; deelname is expliciete opt-in. | |
| Coach mode | Coach krijgt read-access op het account van een atleet; atleet verleent en kan toegang te allen tijde intrekken. | ⚠️ 💭 |

**5B — Profiel & identiteit**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Publiek vs. privé profiel | Standaard privé; publiek maken is een bewuste keuze; per post instelbaar onafhankelijk van profielinstelling. | |
| Meervoudige profieltypen | Een gebruiker kan meerdere profieltypen tegelijk zijn: atleet én schrijver én coach — het profiel toont wat relevant is per type, zonder kunstmatige keuze. | 💭 |
| Meervoudige verified status | Verificatie is per categorie en stapelbaar: iemand kan geverifieerd zijn als coach én als medisch professional én als atleet; elke badge staat op zichzelf; geen betaalde verificatie. | 💭 |
| Following / followers | Asymmetrisch volgen; follower-count is alleen zichtbaar voor de eigenaar van het profiel, nooit publiek als statusgetal. | |
| Per-persoon content-filter | Per gevolgde persoon stel je in wat je op je tijdlijn ziet: alle activiteiten, alleen races/highlights, alleen creatieve posts, alleen journal entries — jij bepaalt de relevantie, niet een algoritme. | |
| Volglijst curatie | Gebruiker beheert volledig wie ze volgen en wat ze zien; geen "mensen die je misschien kent" koude aanbevelingen. | |

**5B² — Audience & deelcontrole**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Volgers categoriseren | Gebruiker deelt eigen volgers in zelfgekozen categorieën: vrienden, hardloopclub, collega's, publiek, coaches — één volger kan in meerdere categorieën zitten. | ⚠️ |
| Deel-instellingen per categorie | Per categorie bepaal je wat zichtbaar is: dagelijkse activiteiten, alleen highlights, gezondheidsdata, journal entries, body metrics — elke categorie heeft een eigen zichtbaarheidsfilter. | ⚠️ R: Volgers categoriseren |
| Deel-instellingen per persoon | Bovenop categorieën: per individuele volger een afwijkende instelling instellen — fijnmazige uitzonderingen zonder het totaalplaatje te verstoren. | ⚠️ R: Volgers categoriseren |
| Standaard zichtbaarheid per inhoudstype | Gebruiker stelt per inhoudstype een standaard in: "activiteiten zijn standaard zichtbaar voor vrienden, journal entries alleen voor mijzelf, tenzij ik ze expliciet deel." | |
| Zichtbaarheidspreview | Voordat je iets post kun je zien wie het precies te zien krijgt op basis van huidige instellingen — geen verrassingen achteraf. | |
| Retroactieve zichtbaarheidswijziging | Eerder gedeelde content achteraf beperken of uitbreiden zonder de post te verwijderen. | ⚠️ |

**5C — De feed**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Chronologische tijdlijn | Feed is altijd chronologisch; geen ranking, geen promoted content, geen verborgen onderdrukking. | |
| Dagelijks feed-eindpunt | Feed heeft een bewust einde: "je hebt alles gezien voor vandaag" — geen laad-meer-loop. | |
| Activiteit delen | Rijke activiteitspost: kaart, key stats, auto-generated story, weerfoto, contextdata — één post vertelt het hele verhaal. | R: Auto-generated activity story |
| Journal entry delen | Schrijver of dichter deelt een reflectie of creatief stuk — met of zonder health-data als stille contextuele laag. | R: Vrije journal entry |
| Creatieve post | Vrije content: tekst, foto, audio — niet gekoppeld aan een activiteit of health-metric; puur menselijke expressie. | 💭 |
| Samengestelde post | Activiteit + journaalreflectie als één post: "de rit, en daarna schreef ik dit." | 💭 R: Vrije journal entry, Activiteit delen |
| Highlight-post | Gebruiker markeert een activiteit of entry als highlight; highlights zijn zichtbaar op het publieke profiel als curated overzicht. | |
| Collecties | Gebruiker bundelt posts in een benoemde collectie: "mijn marathonseizoen 2025 in woorden en data." | 💭 |

**5D — Interactie — bewust gelimiteerd**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Reacties (tekst, minimaal 5 woorden) | Reacties zijn altijd tekst met een minimale lengte; "leuk", "mooi" en "gaaf" zijn als enige reactie niet mogelijk — een bewuste drempel die betekenisvolle interactie boven klikgedrag stelt. | |
| Appreciatie zonder publiekstelller | Equivalent van een like; zichtbaar voor de poster als persoonlijk signaal, nooit als publiek getal voor anderen. | |
| Directe berichten | 1-op-1 tekstberichten tussen wederzijdse volgers; geen ongewenste DMs van mensen die je niet volgt. | ⚠️ |
| Stilte als default | Sociale notificaties (reacties, appreciaties, nieuwe volgers) zijn standaard uitgeschakeld; gebruiker activeert bewust wat relevant is. | |
| Blokkeren & rapporteren | Gebruiker kan accounts blokkeren of rapporteren; geblokkeerde accounts zien het profiel niet meer. | ⚠️ |

**5E — Externe socials — uitposten, nooit integreren**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Auto-post naar Instagram | Na activiteit of post: opt-in één-klik delen naar IG met automatisch gegenereerde activity card visual. | 💭 ⚠️ |
| Auto-post naar X / Facebook / Strava | Zelfde principe voor andere platforms; AthleteCanvas genereert de content, gebruiker keurt goed en kiest platform. | 💭 |
| Strava cross-post | Activiteiten automatisch spiegelen naar Strava voor wie dat ecosysteem blijft gebruiken. | 💭 |
| Open Graph cards | Elke publieke post heeft een rijke linkpreview buiten de app — deelbaar zonder account vereist om te lezen. | |

**5F — Creator & community**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Creator profiel | Schrijvers, coaches, kunstenaars en atleten die hun publieke output via AthleteCanvas centraliseren; geen aparte tool nodig. | 💭 |
| Community spaces | Optionele groepen rondom een thema — hardlopersclub, revaliderende schrijvers, plantaardige atleten; op uitnodiging of redactioneel gecureerd, geen algoritme. | 💭 ⚠️ |
| Leeslijst / bookmarks | Posts van anderen opslaan zonder dat de poster dit weet — puur persoonlijk, geen sociale druk. | |
| Exporteerbare social history | Alle eigen posts, reacties en berichten volledig exporteerbaar — onderdeel van Data Ownership (Cluster 8). | R: Full data export |
| Account pauzeren zonder dataverlies | Sociale aanwezigheid pauzeren: profiel wordt onzichtbaar, alle data bewaard, heractivering zonder verlies mogelijk. | |

---

### Cluster 6 — Health & Wellbeing
*Not just a training log — a picture of the whole person.*

> **Gezondheidsfilosofie**
> AthleteCanvas meet, registreert en legt verbanden — maar moraliseer niet en stelt geen diagnoses. Wat gezond is in een specifieke context is een gesprek met een expert, niet een app-oordeel. Doelen stellen mag, maar altijd data-gedreven en met context: haalbaarheid, tijdshorizon en gedragspatronen worden inzichtelijk gemaakt, nooit beoordeeld. AI treedt deterministisch op — het toont verbanden, signaleert afwijkingen, maar praat de gebruiker nooit naar de mond. *(Zie ook: Cluster 4 — AI-gedragsprincipes)*

**6A — Slaap & herstel**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Sleep dashboard | Slaapoverzicht: duur, fases, HR, HRV, SPO2, ademhaling, score; trend 30 dagen; correlatie met training. | |
| HRV tracking en trends | Ochtend-HRV als trend met 7-daags rolgemiddelde; boven/binnen/onder persoonlijke norm. | |
| Stress tracking | Dagelijkse stressniveau-trend met correlatie naar training, slaap en body metrics. | |
| Resting heart rate trend | Dagelijkse resting HR met annotaties voor load-piekens, ziekte en reizen. | |
| VO2max trend | Geschatte VO2max over tijd, per sport (hardlopen, fietsen). | |

**6B — Stemming & energie**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Mood & energy check-in | Dagelijkse check-in: energie (1–5) en stemming (1–5); input voor AI-correlaties met slaap, voeding en training. | |
| Menstruatiecyclus tracking | Cyclusfases loggen; correlatie met prestatie, herstel en slaap; trainingsplanning houdt er rekening mee. | ⚠️ |

**6C — Voeding**

> **Voedingsfilosofie**
> Voeding is brandstof, geen moreel oordeel. AthleteCanvas registreert wat je eet en legt verbanden met hoe je presteert, herstelt en slaapt — zonder dieet-app taal, streaks op calorie-deficit of gewichtsdoelen als op zichzelfstaande targets. Een gezond gewicht is de uitkomst van een gezonde levensstijl, niet het doel. Gewichtsprojectie op basis van gedragspatronen is beschikbaar als informatief instrument, niet als drijfveer.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Maaltijden loggen | Maaltijden invoeren via handmatige invoer, barcode, spraak of foto; favorieten en templates voor snelle herlog. | |
| Barcode scanner | Productinfo ophalen via Open Food Facts en NEVO; macro's en micro's automatisch ingevuld. | R: Cluster 1 |
| Macro tracking | Dagelijkse en per-maaltijd totalen: kcal, eiwit, koolhydraten, vet; trends over 7/30/90 dagen. | |
| Micro tracking | Vitaminen, mineralen en vezels als langetermijntrend; geen dagelijkse stresserende score maar wekelijks patroon. | |
| Maaltijdtiming overzicht | Visualisatie van wanneer je eet relatief aan slaap, training en dag — eetpatroon als gedragsspiegeling. | |
| Pre/post-workout voeding | Maaltijd markeren als trainingsvoeding; correlatie met prestatie, energie tijdens activiteit en herstel achteraf. | |
| Nuchter trainen log | Fasted training registreren; effect op energie, HR en prestatie zichtbaar als trend. | |
| Energie-balans | Geschatte inname vs. geschat verbruik als informatief dashboard; geen dieet-framing, wel brandstofbewustzijn. | |
| Energy availability score | Dagelijkse EA-berekening op basis van inname en trainingsbelasting; RED-S risicosignalering bij structureel tekort. | R: Cluster 2C |
| Voedingspatroon analyse | Regelmaat van maaltijden, gemiste maaltijden en distributie over de dag als gedragspatroon. | |
| Tekort-signalering | Wekelijks signaal bij structureel laag eiwit, ijzer, vitamine D of andere micro's; geen dagelijkse alarmbel. | |
| Darmsensitiviteit log | Voedingsmiddelen die GI-klachten veroorzaakten markeren; correlatie met trainingsprestatie en herstel. | 💭 |
| Voedingsnotitie | Vrije tekst bij een maaltijd voor context: hoe voelde het, bijzondere omstandigheid, sociaal eten. | |

**6D — Hydratatie & cafeïne**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Waterinname logging | Dagelijkse vochtinname loggen per drinkmoment; wekelijkse totalen en laag-intake-alerts. | |
| Dranktype registratie | Onderscheid water, elektrolytendrank, koffie, alcohol; bijdrage aan hydratatiestatus per type. | |
| Zweet-gecorrigeerd dagdoel | Hydratatiedoel aangepast op basis van trainingsbelasting en temperatuur van de dag. | R: Cluster 1 |
| Cafeïne tracking | Cafeïne-inname loggen; timing relatief aan slaap en training; koppeling aan slaapkwaliteit als correlatie. | R: Cluster 2B |
| Alcohol impact tracker | Alcoholconsumptie loggen; zichtbaar als annotatie op slaap-, HRV- en herstelgrafieken. | R: Cluster 2B |

**6E — Lichaamscompositie**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Gewicht trend | Lichaamsgewicht als langetermijntrend; dagruis gefilterd naar 7-daags gemiddelde. | |
| Lichaamssamenstelling | Vetpercentage, spiermassa, botdichtheid als trend waar meetdata beschikbaar is (weegschaal, DEXA). | 💭 |
| Gewichtsprojectie | Verwacht gewichtsverloop op basis van huidige voedings- en trainingspatroon; informatief, niet normatief. | |
| Gewichtsdoel met context | Gewichtsdoel instellen is mogelijk maar altijd gekoppeld aan tijdshorizon, haalbaarheidscheck en gedragspatronen; geen geïsoleerd streefgetal zonder context. | |

**6F — Supplementen**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Supplementen schema | Supplementen loggen met dosering en timing; persoonlijk dagschema met evidence-based context per supplement. | 💭 |
| Supplement interaction check | Signalering van bekende interacties tussen door de gebruiker gelogde supplementen. | 💭 ⚠️ |
| Supplement stock tracking | Voorraad per supplement bijhouden; melding wanneer voorraad op basis van schema bijna op is. | 💭 |

**6G — Medisch & revalidatie**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Ziekte en blessure log | Ziekte of blessure loggen met begin-/einddatum; zichtbaar als annotatie op alle tijdlijngrafieken. | |
| Medication tracking | Medicatie loggen voor persoonlijke correlatie-tracking; nooit gedeeld of gebruikt voor benchmarks. | ⚠️ |
| Sportblessure revalidatie | Blessure loggen (type, locatie, ernst); trainingsuggesties aangepast; stapsgewijs terugkeer-protocol. | |
| Medische revalidatie | Medische beperking of herstelperiode na ziekte/ongeval loggen; planning en voortgang worden aangepast. | 💭 ⚠️ |

---

### Cluster 7 — Mobile & Interface
*Het echte leven is de interface — de app is de terugvaloptie.*

> **UX-grondwet**
> AthleteCanvas haalt waarde uit het echte leven, niet uit schermtijd. Elke interactie moet verdiend zijn: relevant, tijdig en actionable. Meer schermtijd is nooit een succesmetric — minder is beter. Dit principe geldt voor alle interfaces: mobiele app, web, widget, wearable en notificatie.
>
> **Expliciete anti-patterns — nooit implementeren:**
> - Geen welkomstschermen of splash screens die toegang vertragen
> - Geen streaks of badges als doel op zich
> - Geen "je hebt X dagen niet ingelogd" notificaties
> - Geen autoplay, geen infinite scroll
> - Geen notificaties die alleen bedoeld zijn om de app te openen
> - Geen teaser-widgets die bewust informatie achterhouden om een app-open te forceren
> - Geen engagement-dashboards, geen DAU-targets als productdoel
>
> Widgets bestaan om app-opens te *vervangen*. Notificaties bestaan om je in het echte leven te informeren zodat je de app *minder* hoeft te openen.

**7A — Native apps**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Full iOS app | Native iOS-app met alle features; Face ID, share sheet, shortcut actions. | |
| Full Android app | Native Android-app met alle features; biometrie, share intents, adaptive icons. | |
| Offline access | Dashboard, laatste 30 dagen activiteiten en profiel beschikbaar zonder internet; sync bij herstel verbinding. | |
| Dark mode | Volledige dark mode op iOS en Android, systeemvoorkeur gerespecteerd. | |
| Haptic feedback | Haptics bij sync-completion, mijlpaal en bevestiging van destructieve acties — nooit als nudge. | |
| App activation sync | Openen van de app triggert automatisch een sync op de achtergrond. | |
| Workout import from device | Workout direct importeren van Garmin of Apple Watch zonder tussenkomst van een platform. | 💭 ⚠️ |

**7B — Widgets & glanceable design**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Home screen widgets | Small/medium/large widgets volledig informatief: activiteitssamenvatting, recovery score, weekdoelvoortgang, Body Battery — geen teasers. | |
| Lock screen widgets | Compacte lock screen widget met meest relevante metric van het moment (recovery, volgende sessie, dagdoel). | |
| Dynamic Island / live activities | Real-time sync-status of actieve workout zichtbaar als live activity; verdwijnt automatisch als relevant. | 💭 |
| Standby / bedside mode | Nachtkast-weergave: slaaptimer, rustige klok, geen notificaties — scherm als stilteruimte. | 💭 |

**7C — Notificaties**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Notificatiecategorieën | Notificaties ingedeeld in: actionable (sync-fout, re-auth), informatief (PR, recovery-alert), sociaal (reactie, mention) — per categorie aan/uit. | |
| Wekelijks digest als alternatief | Niet-urgente notificaties optioneel bundelen tot één wekelijkse samenvatting in plaats van afzonderlijke pushes. | |
| Focus mode integratie | iOS Focus en Android DND worden gerespecteerd; app past eigen notificatiegedrag automatisch aan op stille periodes. | |
| Schermtijd-bewustzijn | Optionele inzage in eigen app-gebruik: wanneer open je de app, hoe lang, via welke route — transparantie zonder veroordeling. | 💭 |

**7D — Quick log & spraak**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Quick log vanuit widget | Mood, hydratatie, maaltijd of notitie loggen direct vanuit widget of lock screen in <30 seconden zonder app te openen. | |
| Spraakdicteer invoer | Gesproken invoer via on-device spraak-naar-tekst voor alle logvormen; "havermout met banaan, koffie" → macro-entry. | |
| Conversationele log extractie | Vrije gesproken of getypte zin wordt door NLP omgezet naar gestructureerde log-entries over meerdere domeinen tegelijk. | 💭 ⚠️ R: Cluster 4 |
| Snelle actie vanuit notificatie | Notificaties met directe actieknop: log bevestigen, sync starten, snooze recovery-alert — zonder de app te openen. | |

**7E — Conversationele interface (aspirationeel)**

> Volledig bi-directioneel gesprek met de app — "Hoe reageerde ik de afgelopen twee weken op hoge trainingsload?" — is het eindpunt van deze visie. AthleteCanvas levert de datacontext; de gebruiker kiest zijn eigen AI-provider via een **Bring Your Own AI** principe. Vendorlockout wordt uitgesloten, data-soevereiniteit geborgd.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Gesproken query interface | De gebruiker stelt vragen aan zijn eigen data via spraak; antwoord wordt gesproken en/of visueel teruggegeven. | 💭 ⚠️ R: Cluster 4 |
| Bring Your Own AI (BYOA) | Gebruiker koppelt eigen AI-provider (Gemini, ChatGPT, lokaal model, Whisper); AthleteCanvas levert de context-laag. | 💭 ⚠️ R: Cluster 1, Cluster 8 |
| Externe agent integratie | Integratiepunten voor Gemini on-device, ChatGPT, WhisperFlow of andere agents als verwerkingsprovider naast interne AI. | 💭 ⚠️ R: Cluster 1 |
| Conversatiegeschiedenis | Gestelde vragen en gegeven antwoorden bewaard als persoonlijk log; inzichtelijk en verwijderbaar. | 💭 R: Cluster 8 |

**7F — Smartwatch extensie**

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Apple Watch companion app | Watchface met recovery score, dagdoel, Body Battery; quick log vanuit pols; workout-start vanuit horloge. | 💭 |
| WearOS companion app | Zelfde functionaliteit als Apple Watch voor WearOS-horloges; Garmin als primaire databron blijft apart. | 💭 |
| Wearable quick log | Mood, hydratatie of korte notitie dicteren of tikken via smartwatch zonder telefoon erbij. | 💭 |

---

### Cluster 8 — Data Ownership & Privacy
*The user's data belongs to the user. AthleteCanvas is a steward, not an owner.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Full data export | Volledige data-export op elk moment: activities, sleep, body metrics, notes, tags, profiel, AI-history, sync logs. | |
| Account deletion with full data removal | Account verwijderen wist alle data permanent uit alle systemen; onomkeerbaar, duidelijk gewaarschuwd. | |
| Consent management | Overzicht van alle verleende toestemmingen per databron en gebruik; elk afzonderlijk in te trekken. | |
| Social data sharing consent | Elke cross-user datadeling vereist expliciete individuele toestemming; geen impliciete of ambient sharing. | |
| Privacy dashboard | Één scherm: gekoppelde bronnen, opgeslagen datacategorieën, actieve delingsrelaties, community opt-ins, export/delete. | |
| Granular data visibility controls | Gebruiker bepaalt per datacategorie wat gedeeld wordt voor benchmarks of sociale features. | |
| Sensitive data separate consent | Bloedgroep, medische info, medicatie en cyclusdata vereisen apart expliciete toestemming; per veld verwijderbaar. | ⚠️ |
| Transparency in AI processing | Begrijpelijke uitleg van welke data gebruikt wordt voor welke AI-feature; re-consent bij wijziging. | |

---

### Cluster 9 — Planning & Goals
*Not just what the user did — but what they are working towards.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Trainingsplan generator | Op basis van doel en tijdlijn een gestructureerd plan met periodisering en taper genereren; past aan bij gemiste sessies. | ⚠️ |
| Methodiek-bibliotheek | Bibliotheek van trainingsmethodieken (polarized, pyramidaal, HIT, base building) met uitleg; koppelbaar aan plan. | 💭 |
| Workout builder | Losse sessies opbouwen uit intervallen, sets, zones; opslaan in bibliotheek en hergebruiken in plannen. | |
| Periodisering | Trainingsblokken structureren in macro/meso/microcyclus; visuele weergave en adherentie-tracking. | 💭 |
| Geplande vs. uitgevoerd | Geplande sessies vergelijken met wat daadwerkelijk geïmporteerd is. | R: Trainingsplan generator |
| Load management & overtraining signalering | Monitoring van trainingsbelasting vs. plan; signalering bij te snelle opbouw of onderprestatie. | R: Training load analysis |
| Tapering planner | Automatische taper-berekening in de weken voor doelevenement op basis van methodiek en history. | R: Evenementenkalender |
| SMART-doelen | Tijdgebonden meetbare doelen instellen: gewicht, racetijd, weekvolume; voortgang-tracking. | |
| Evenementenkalender | Races en events toevoegen met datum en A/B/C-prioriteit; ankerpunt voor het trainingsplan. | |
| Race-day plan | Specifiek plan voor de eventdag: pacing, voedingstiming, gear-checklist, warming-up; auto-generated en bewerkbaar. | 💭 |
| Post-event evaluatie | Na een event finish-tijd, beleving en positie loggen; voorspeld vs. werkelijk vergeleken. | |

---

### Cluster 10 — Kookboek & Boodschappen
*From nutrition goal to grocery cart — without thinking about it.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Receptenbibliotheek | Receptenbibliotheek gefilterd op dieetprofiel, allergieën en voorkeuren; macro-breakdown per portie. | 💭 |
| Dieetprofiel | Dieetvoorkeuren instellen (vegan, glutenvrij, keto, etc.); allergieën als harde uitsluiting, voorkeuren als zachte filter. | |
| Maaltijdplanner | Weekplanning van maaltijden uit de receptenbibliotheek; dagelijkse macro-totalen vs. persoonlijk doel. | R: Dieetprofiel |
| Portie-aanpassing | Recepten automatisch schalen op aantal porties of dagelijkse macrodoelen. | |
| Voeding gekoppeld aan training | Pre- en post-workout maaltijdsuggesties op basis van sessietype; planner houdt trainingskalender bij. | R: Trainingsplan generator |
| Boodschappenlijst generator | Vanuit weekplanning automatisch een gecombineerde, ontdubbelde boodschappenlijst genereren. | R: Maaltijdplanner |
| Supermarkt-integratie fase 1 | Boodschappenlijst exporteren als PDF of in formaat dat compatibel is met gangbare boodschappen-apps. | |
| Supermarkt-integratie fase 2 | Directe koppeling met AH, Picknick, Flink: bestelling plaatsen vanuit de app zonder AthleteCanvas te verlaten. | 💭 ⚠️ |
| Prijs-vergelijker | Hetzelfde product tonen bij meerdere supermarktpartners met prijs. | 💭 |
| Periodieke voeding | Voedingsstrategieën per trainingsfase: carb loading, cutting, bulking; macro-doelen automatisch aangepast. | R: Trainingsplan generator |

---

### Cluster 11 — Coach & Professional Portal
*A B2B layer on top of the consumer app.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Coach portaal | Dedicated web portaal voor coaches; meerdere cliënten beheren, volledig dashboard per atleet. | ⚠️ |
| Multi-atleet overzicht | Overzichtspagina met alle cliënten: recovery score vandaag, load-trend, geflagde anomalieën per atleet. | |
| Coach annotaties | Coach voegt notities en feedback toe aan activiteiten van een cliënt, zichtbaar in de eigen app van de atleet. | R: Coach portaal |
| Coach-gestuurde doelen | Coach stelt trainingsdoelen of een plan in voor een cliënt; zichtbaar naast of in plaats van AI-suggesties. | R: Coach portaal |
| Fysiotherapeut view | Beperkte read-only toegang voor fysiotherapeut: blessurelog, trainingsbelasting, slaap/herstel. | 💭 ⚠️ |
| Diëtist koppeling | Diëtist kan voedingslog, body metrics en supplementenschema van cliënt inzien. | 💭 R: Nutrition logging |
| White label voor coaches | Coach kan het portaal en de cliëntomgeving branden met eigen naam/logo ("Powered by AthleteCanvas"). | 💭 |

---

### Cluster 12 — Monetisatie & Partnerships
*How AthleteCanvas sustains itself and grows — without compromising user trust.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Subscription model (lagen) | Freemium met duidelijke betaalde tiers; basisfunctionaliteit gratis, AI/planning/pro-integraties betaald. | 💭 |
| Early adopter programma | Gebruikers vóór een cutoff-datum krijgen extended free tier of lifetime-korting; drijft early community-groei. | 💭 |
| Lifetime deal | Eenmalige aankoopoptie voor permanent toegang tot een tier; tijdelijk beschikbaar voor urgentie en vroeg kapitaal. | 💭 |
| Affiliate — voeding & supplementen | Commissie op doorverwijzingen naar supplement- en sportvoedingsmerken; transparant gelabeld, nooit op basis van health-data. | 💭 |
| Supermarkt partnership | Affiliatevergoeding of commissie per bestelling via boodschappen-integratie. | R: Supermarkt-integratie fase 1 |
| Event partnerships | Races en events in de evenementenkalender; referral-vergoeding bij inschrijving via de app. | R: Evenementenkalender |
| Zorgverzekeraar partnerships | Opt-in schema waarbij gebruiker geanonimiseerde data deelt met verzekeraar in ruil voor premiekorting. | 💭 ⚠️ |
| Coach revenue share | Percentage op omzet die een coach genereert via het platform (bijv. white label trainingsplannen). | 💭 R: Coach portaal |
| Referral programma | Gebruiker nodigt vriend uit; beide ontvangen beloning (gratis maand, korting); virale groei zonder betaalde acquisitie. | |
| Advertentiemodel (niet-gepersonaliseerd) | Contextuele gesponsorde content in relevante app-secties; health-data nooit gebruikt voor targeting; duidelijk gelabeld. | 💭 |

### Cluster 13 — Journal & Reflection
*Een bewust andere interface — rustig, tekstgedreven, reflectief — maar met alle AthleteCanvas-data als stille ruggengraat.*

**13A — Data-aware journaling** — schrijven met context die er gewoon al is.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Vrije journal entry | De gebruiker schrijft vrij; de app hecht automatisch een contextsnapshot aan elke entry: HRV, slaap, gedane training, weer, pollen, loadstatus — zonder dat de gebruiker iets hoeft in te vullen. | |
| Tijdlijn met journaalentries | Terugkijken combineert journal-entries met biologische en trainingsdata op dezelfde tijdlijn: "wat schreef ik" + "wat was er aan de hand toen ik dit schreef." | |
| Doorzoekbaar journaal | Vrije-tekst zoekfunctie over alle entries, inclusief metadata-filters: "toon entries geschreven op slechte slaapnachten" of "entries in opbouwfase." | |
| Foto's en bijlagen in journal | Foto's, audio-notities of bestanden toevoegen aan een journaalentry. | 💭 |

**13B — AI als journaalpartner** — niet een dashboard dat terugpraat, maar een gesprek.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Contextuele AI-respons op entry | Na het schrijven kan de gebruiker vragen om AI-respons: de AI reageert conversationeel en koppelt de inhoud aan relevante data — geen grafiek, maar een antwoord. | ⚠️ |
| Patroonherkenning over entries | AI detecteert terugkerende thema's in journaalentries over tijd: "Je schrijft vaker over motivatieproblemen in de derde week van een opbouwblok." | 💭 ⚠️ |
| Emotie- en toonanalyse | Optionele sentimenttrend over journaalentries: energie, positiviteit, stress — zichtbaar naast de biologische data. | 💭 ⚠️ |

**13C — Structuur voor wie dat wil** — prompted templates, bullet-journal stijl.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Ochtendintentie prompt | Korte dagelijkse ochtendprompt: "Wat is mijn focus vandaag? Hoe voel ik me?" — prefilled met recovery score en geplande sessie. | |
| Avondreflectie prompt | Korte avondprompt: "Wat ging goed vandaag? Wat leerde ik? Hoe sliep ik (verwacht)?" | |
| Wekelijkse review template | Gestructureerde wekelijkse terugblik: wat ging goed, wat niet, focus komende week — prefilled met weekdata. | |
| Maandelijkse retrospective | Maandoverzicht met journaalentries, doelvoortgang en gezondheidstrend als geïntegreerde samenvatting. | |
| Future log | Intenties en plannen voor komende periodes vastleggen; koppelbaar aan evenementen en doelen. | 💭 |
| Gewoontetracker (zelf te definiëren) | Gebruiker definieert eigen gewoontes (mediteren, flossen, buiten geweest, alcohol-vrij, etc.); dagelijks bijgehouden en correleerbaar met prestatie- en hersteldata. | |

**13D — Cognitieve en productiviteitsdimensie** — atletische én mentale prestatie als één geheel.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Focusscore logging | Dagelijkse cognitieve scherpte (1–5); gecorreleerd met slaap, training, voeding en supplementen. | 💭 |
| Creativiteit & mentale energie | Optionele dagelijkse score voor mentale energie en creativiteit; correlatie met trainingslast en herstelniveau. | 💭 |
| Cognitieve prestatie vs. training | "Je gemiddelde focusscore op dagen na een ochtendsessie is significant hoger dan op niet-trainingsdagen." — inzicht voor kenniswerkers en atleten tegelijk. | 💭 |
| Non-athlete gebruik | De app is bruikbaar zonder enig gekoppeld apparaat: puur als data-aware journal + habit tracker + gezondheidslog, voor gebruikers die geen sportuur hebben maar wel bewust met gezondheid en productiviteit bezig zijn. | 💭 |

**13E — Journal mode UX** — een bewuste interface-wissel.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Journal mode interface | Een aparte interface-modus: minimalistisch, geen widgets of grafieken, rustig kleurpalet, tekstgedreven — bewust anders dan het dashboard. | 💭 ⚠️ |
| AI opt-in per entry | De AI-integratie is per entry opt-in: "wil je dat ik dit contextualiseer?" — niet automatisch opgedrongen; de gebruiker bepaalt wanneer de data het gesprek binnenkomt. | |
| Offline journaling | Journal entries kunnen offline geschreven worden; sync bij herstel verbinding. | R: Offline access |

---

---

### Cluster 14 — Informatie- & Aandachtsdieet

*We tracken onze macro's voor eten. Waarom tracken we ons mentale dieet niet? Aandacht is een eindige hulpbron — en de meeste platforms roven hem.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Cognitive load integratie | Koppeling met iOS Screen Time / Android Digital Wellbeing: niet alleen "hoeveel uur scherm", maar wát — actief creëren vs. passief consumeren. | 💭 ⚠️ |
| Doomscroll-correlator | AI legt het directe verband: "Op dagen dat je >45 min op sociale media zit, daalt je slaapkwaliteit met 12% en is je ochtend-HRV lager." Geen moraal — gewoon data. | 💭 |
| Informatie macro's | Schermtijd gecategoriseerd als 'Junk' (socials, nieuwsfeeds, passief scrollen) vs. 'Deep Work' (lezen, creëren, coderen, leren) — het mentale equivalent van voedingscategorieën. | 💭 |
| Focus- & flow-detectie | De app herkent periodes van 'flow': geen telefoon opgepakt, stabiele lage hartslag tijdens werk. Markeert deze als positieve biologische events — en correleert ze met slaap en herstel. | 💭 |
| Nieuws-detox tracking | Bewust loggen van dagen zonder nieuwsconsumptie; effect op wekelijkse stress-baseline zichtbaar als correlatie, niet als prestatie-indicator. | 💭 |
| Aandachtsbudget | Dagelijkse "aandachtsbankrekening": hoeveel diepe concentratie heb je al gebruikt, hoeveel is er nog over en waar gaat het volgende uur naartoe? | 💭 |
| Passieve vs. actieve schermtijd trend | Langetermijntrend van de verhouding consumeren vs. produceren op scherm — verschuift het patroon naarmate je bewuster wordt? | 💭 |

---

### Cluster 15 — Digitale Soevereiniteit & Offline Beloningen

*Softwarematige vertaling van de charging hub-visie: tijd offline is waardevol. De app beloont en maakt het zichtbaar.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Slaapkamer-slot (software zen-modus) | Instelbare tijdvensters (bijv. 21:00–07:00) waarbij de app overschakelt naar zen-modus: alleen journal bereikbaar, alle andere app-notificaties geblokkeerd. De telefoon wordt een nachtboek, niet een prikkelcentrum. | ⚠️ |
| Unplugged Credits | Uren buiten slaaptijd waarop de telefoon niet ontgrendeld is, leveren 'Sovereign Credits' op — een persoonlijke score die toont hoe goed je loskomt van digitale afleiding. Geen gamificatie-druk; puur informerend. | 💭 |
| Local-first / on-device processing | De app functioneert voor 90% offline. AI-analyses draaien waar mogelijk lokaal op het apparaat. Data verlaat het device alleen bij expliciete, door de gebruiker geïnitieerde sync. | ⚠️ |
| Partner / lokale beloningen | Unplugged Credits verzilveren bij lokale 'bewuste' ondernemers (koffiezaak, boekwinkel, kapper, restaurant) die schermvrije aanwezigheid belonen via QR-check-in. De hardware-versie van de charging hub — maar puur softwarematig. | 💭 ⚠️ |
| Device-vrije zones instellen | Gebruiker definieert locaties (thuis, werk, café) met bijbehorend aanbevolen gedragsprofiel; app past zen-modus en notificatie-instellingen automatisch aan op locatie. | 💭 |
| Digitale soevereiniteitscore | Een wekelijkse samenvattingsscore: hoe soeverein was je deze week t.o.v. digitale afhankelijkheid? Samengesteld uit schermtijd, zen-modus-uren, offline credits en doomscroll-data. | 💭 |

---

### Cluster 16 — Life Capacity & Biologische Ritmes

*Van "Ben ik klaar om te sporten?" naar "Ben ik klaar om te leven?" De data-gedreven persoonlijk energiemanager.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Life Battery / bandbreedte | Een holistische dagelijkse capaciteitsscore: kun je die zware vergadering aan? Heb je bandbreedte voor een creatief project vanavond? Of zit je in overlevingsmodus? Vertaalt biologische data naar dagelijkse capaciteit — niet alleen voor sporters. | |
| Work vs. recovery balance | Agenda-koppeling analyseert het type werkdag: veel calls vs. diepe focustijd. Past avond-advies aan: "Je had 6 uur meetings. Ga niet naar een drukke sportschool — ga wandelen in het bos." | 💭 R: Agenda-integratie |
| Biologische seizoenen | Herkenning van langetermijnritmes over jaren: "Je energieniveau dipt steevast in november. Laten we je belasting dit jaar preventief aanpassen." Circadiaan op jaarbasis. | 💭 |
| Ziekte als reset, niet als verlies | Zodra de gebruiker 'ziek' logt, pauzeert alles. Geen dalende grafieken, geen waarschuwingen, geen gemiste-doelen-meldingen. De app schakelt over naar herstel-modus: ook dag 3 zonder activiteit wordt niet aangemerkt als slechte week. | |
| Biologisch leeftijd vs. kalenderleeftijd | Op basis van VO2max, resting HR, slaapkwaliteit, HRV en trainingshistorie een indicatieve biologische leeftijdsschatting. Niet als cijferfetisjisme, maar als langetermijn-motivatie-inzicht. | 💭 ⚠️ |
| Energietype-analyse | Niet iedereen haalt energie uit dezelfde dingen. De app detecteert welke activiteiten (sport, sociale interactie, creatief werk, natuur, rust) jouw biologische markers het meest herstellen — en welke uitputten. | 💭 |
| Creatieve bandbreedtecyclus | Herkenning van wanneer het brein leeg is en produceren geen zin meer heeft — relevant voor makers, schrijvers, programmeurs. Voorkomt creatieve uitputting door het moment van push zichtbaar te maken. | 💭 |

---

### Cluster 17 — Persoonlijke Waarheidsvinding (Truth Engine)

*Jouw data als leugendetector voor zelfbedrog, placebo's en omgevingsruis. De app helpt filteren wat écht is.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Symptoom-leugendetector | Gebruiker klaagt in journal: "Ik ben zo moe, alles is zwaar." AI antwoordt zachtjes: "Je HRV is optimaal en je hebt 8 uur geslapen. Is dit fysieke vermoeidheid, of mentale weerstand?" Niet oordelend — maar eerlijk. | 💭 ⚠️ |
| Placebo tracker | Gebruiker start nieuw supplement, dieet of gewoonte (koud douchen, magnesium, intermittent fasting). De app vergelijkt ervaren voordelen uit het journal met harde biologische data over dezelfde periode: werkt het echt, of is het placebo? | 💭 |
| Omgevingsimpact analyse | Correlaties tussen locatie/werkplek en gezondheid: "Op kantoor (locatie X) is je stress 20% hoger en je stappenaantal 40% lager dan op thuiswerkdagen." Zichtbaar zonder veroordeling. | |
| "What if" levenskeuze simulator | "Wat voorspelt de data als ik stop met alcohol en een uur per dag minder op mijn telefoon zit — over een periode van 6 maanden?" Scenario's op basis van eigen historische patronen. | 💭 |
| Zelfperceptie vs. data vergelijker | De gebruiker scoort zijn eigen week subjectief (energie, prestatie, focus) naast de objectieve metingen. Calibreert zelfkennis over tijd: leer je beter inschatten hoe je het doe? | 💭 |
| Betrouwbaarheid van databronnen | De app toont transparant hoe betrouwbaar elke meting is: "HRV gemeten met optische sensoren is indicatief, niet klinisch. Zie het als richting, niet als diagnose." Eerlijkheid over data-limieten. | |
| Correlatie vs. causaliteit disclaimer | Elk getoond verband wordt geframed als correlatie, niet als oorzaak. De app cultiveert datakritisch denken in plaats van blinde autoriteit door cijfers te claimen. | |

---

### Cluster 18 — Platform Architectuur & Ethiek

*De filosofie in code gegoten. Dit cluster beschrijft niet hoe het platform gebouwd is — maar welke platformkeuzes de gebruiker ziet en kan vertrouwen.*

> **Opmerking:** dit cluster beschrijft verwachtingen en commitments vanuit gebruikersperspectief, niet implementatiedetails. Technische invulling hoort thuis in `docs/planning/issue16/research.md`.

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Open core engine | De basis-infrastructuur (data-ingestie, opslag, encryptie, basis-dashboard) is open source en publiek inspecteerbaar. Gebruikers kunnen de code lezen, auditen en zelf hosten. | ⚠️ |
| Premium AI & coach laag | Geavanceerde AI-inzichten, Truth Engine, planning en B2B-integraties zijn de betaalde abonnementslaag — het eerlijke model dat het platform financiert. | |
| Zero-engagement codebase | De app bevat geen mechanismen voor engagement farming: geen push-notificaties zonder actieve gebruikersinstructie, geen algoritmische contentranking, geen re-engagement triggers. Architectureel geborgd, niet beleidsmatig beloofd. | ⚠️ |
| Cryptografische data-deletie | Account verwijderen = cryptografische vernietiging van de encryptiesleutels. Data is onomkeerbaar onleesbaar, ook op backups. Geen "inactief"-vlaggetje in een database. | ⚠️ |
| Portable identity | Volledig profiel, AI-geschiedenis en alle data exporteerbaar in universeel formaat. De gebruiker is niet gegijzeld door het platform — hij kan vertrekken met alles wat van hem is. | 💭 |
| Geen model-training op persoonlijke data | Persoonlijke gezondheidsdata wordt nooit gebruikt om algemene AI-modellen te trainen zonder expliciete opt-in per gebruik. De AI leert over jou, van jou — niet over anderen, van jou. | ⚠️ |
| Transparant businessmodel | Het verdienmodel is op elk moment leesbaar en eenvoudig: abonnement, open core, geen advertenties, geen data-verkoop. Geen verborgen agenda — ook niet in de kleine lettertjes. | |

---

### Cluster 19 — Nieuwe Doelgroepen

*Energiebeheer is niet alleen voor sporters. Voor sommige groepen is het geen hobby — het is een voorwaarde om te kunnen functioneren.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Neurodivergent modus (ADHD/autisme) | Focus op sensorische overprikkeling, dopamine-management (pieken/dalen na hyperfocus) en herstel van executieve disfunctie. Prikkel-correlatie in plaats van sport-correlatie als primaire lens. | 💭 |
| Chronisch ziek / burnout (pacing & spoon theory) | Energie is eindig en niet-herstelbaar door extra inspanning. Visueel maken van het energiebudget (spoons) per dag, om crashes te voorkomen bij Long Covid, ME/CVS, zware burnout of postoperatief herstel. | 💭 |
| Survival ouders | Modus voor jonge ouders zonder slaap, zonder routine, zonder energie voor doelen. Geen rode grafieken, geen gemiste doelen, geen suggesties om meer te sporten. Puur schadebeperking: "Je hebt 3 uur geslapen in blokjes. Vandaag telt overleven — wees mild voor jezelf." | 💭 |
| De creatieve maker | Tracking van flow-state stamina en creatieve cycli voor artiesten, schrijvers, muzikanten, programmeurs. Herkennen wanneer het brein leeg is en pushen contraproductief wordt — preventie van creatieve burn-out als primaire waarde. | 💭 |
| Kenniswerker zonder sport | De app volledig bruikbaar zonder een enkel gekoppeld sportapparaat: puur journal, habit tracker, slaap, stress, cognitieve performance, aandachtsdieet. Data-gedreven zelfkennis zonder atleet te zijn. | 💭 |
| Mantelzorger / verzorgende professional | Mensen die professioneel of privé de zorg voor anderen dragen verliezen systematisch hun eigen basisbehoefte uit oog. Een modus die de focus legt op de zorgverlener zelf: herstel, grenzen, slaap, eigen energie. | 💭 |
| Ouder wordende actieve mens | Niet de sporter die traint voor PR's, maar de 55-plusser die actief wil blijven zonder blessures. Herstel duurt langer, volumes zijn lager, continuïteit telt zwaarder dan piekprestatie. Aangepaste metrics en benchmarks. | 💭 |

---

### Cluster 20 — Anti-Viral Social Sanctuary

*Een sociale laag die door zijn architectuur virality, engagement farming en de race-to-the-bottom van aandachtseconomie onmogelijk maakt.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Zero-discovery architectuur | Er is geen 'For You', geen 'Explore', geen aanbevelingsalgoritme. Je kunt alleen mensen vinden via een directe link, QR-code of expliciete zoekopdracht. Viraliteit is technisch onmogelijk — niet beleidsmatig verboden. | ⚠️ |
| Context-first publishing | Een artiest, schrijver of atleet deelt niet alleen het eindproduct, maar hecht er automatisch de biologische realiteit achter: hartslag tijdens het optreden, slaapdata tijdens het schrijven, loadstatus voor de race. De post toont de menselijke werkelijkheid achter de creatie. | 💭 |
| Creator-patron model (sovereign subscriptions) | Creators kunnen diepere dagboeken, data-verrijkte werkprocessen of lange reflecties achter een persoonlijke betaalmuur zetten voor echte fans — Substack/Patreon-principe, maar geïntegreerd en eigendomsbevrijdend. Minimale platform-fee; creators bezitten hun publiek volledig. | 💭 ⚠️ |
| Het 'Studio' profiel | Een profiel is geen etalage van hoogtepunten maar een logboek van het proces: ruwe werkethiek, moeilijke momenten, data over de weg ernaar toe. Authenticiteit afgedwongen door design — niet als norm van buiten opgelegd. | 💭 |
| Volgrelaties als kwaliteitsfilter | Geen follower-count als statussignaal. Volgen is een persoonlijke curation-daad, geen competitie. Wie je volgt en hoe ze bijdragen aan jouw welzijn — dat is het meetpunt, niet hoeveel mensen jou volgen. | |
| Besloten community circles | Kleine, door uitnodiging of curation gevormde groepen rondom gedeeld thema of levensfase. Geen open communities die tot schreeuwwedstrijden verworden — bewust klein gehouden als kwaliteitsbodem. | 💭 |
| Exporteerbare creator history | Alle posts, reflecties en data-verrijkte content volledig exporteerbaar in open formaat. De creator verliest niets als hij het platform verlaat. | R: Full data export |

---

### Cluster 20 — Anti-Viral Social Sanctuary

*Een sociale laag die door zijn architectuur virality, engagement farming en de race-to-the-bottom van aandachtseconomie onmogelijk maakt.*

| Feature | Beschrijving | Flags |
|---------|-------------|-------|
| Zero-discovery architectuur | Er is geen 'For You', geen 'Explore', geen aanbevelingsalgoritme. Je kunt alleen mensen vinden via een directe link, QR-code of expliciete zoekopdracht. Viraliteit is technisch onmogelijk — niet beleidsmatig verboden. | ⚠️ |
| Context-first publishing | Een artiest, schrijver of atleet deelt niet alleen het eindproduct, maar hecht er automatisch de biologische realiteit achter: hartslag tijdens het optreden, slaapdata tijdens het schrijven, loadstatus voor de race. De post toont de menselijke werkelijkheid achter de creatie. | 💭 |
| Creator-patron model (sovereign subscriptions) | Creators kunnen diepere dagboeken, data-verrijkte werkprocessen of lange reflecties achter een persoonlijke betaalmuur zetten voor echte fans — Substack/Patreon-principe, maar geïntegreerd en eigendomsbevrijdend. Minimale platform-fee; creators bezitten hun publiek volledig. | 💭 ⚠️ |
| Het 'Studio' profiel | Een profiel is geen etalage van hoogtepunten maar een logboek van het proces: ruwe werkethiek, moeilijke momenten, data over de weg ernaar toe. Authenticiteit afgedwongen door design — niet als norm van buiten opgelegd. | 💭 |
| Volgrelaties als kwaliteitsfilter | Geen follower-count als statussignaal. Volgen is een persoonlijke curation-daad, geen competitie. Wie je volgt en hoe ze bijdragen aan jouw welzijn — dat is het meetpunt, niet hoeveel mensen jou volgen. | |
| Besloten community circles | Kleine, door uitnodiging of curation gevormde groepen rondom gedeeld thema of levensfase. Geen open communities die tot schreeuwwedstrijden verworden — bewust klein gehouden als kwaliteitsbodem. | 💭 |
| Exporteerbare creator history | Alle posts, reflecties en data-verrijkte content volledig exporteerbaar in open formaat. De creator verliest niets als hij het platform verlaat. | R: Full data export |

---

## Related Documentation
- **[docs/planning/issue16/research.md][related-1]**
- **[docs/planning/issue1/planning.md][related-2]**

<!-- Link definitions -->
[related-1]: docs/planning/issue16/research.md
[related-2]: docs/planning/issue1/planning.md

---

## Version History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 3.7 | 2026-03-07 | Agent | Cluster 7 herschreven als "Mobile & Interface": UX-grondwet manifest + 6 subclusters (7A native apps, 7B widgets/glanceable, 7C notificaties, 7D quick log/spraak, 7E conversationele interface/BYOA aspirationeel, 7F smartwatch extensie). |
| 3.6 | 2026-03-07 | Agent | Cluster 6 volledig herschreven: gezondheidsfilosofie-noot + 7 subclusters (6A slaap/herstel, 6B stemming, 6C voeding met voedingsfilosofie, 6D hydratatie/cafeïne, 6E lichaamscompositie, 6F supplementen, 6G medisch). Voeding als volwaardige derde pijler. Gewichtsdoel-met-context principe. |
| 3.5 | 2026-03-07 | Agent | Cluster 5B uitgebreid: meervoudige profieltypen en verified status; nieuw subcluster 5B² Audience & deelcontrole (volgers categoriseren, per-categorie en per-persoon zichtbaarheidsfilter, zichtbaarheidspreview, retroactieve wijziging). |
| 3.4 | 2026-03-07 | Agent | Cluster 5 volledig herschreven met platformfilosofie anti-features manifest en 6 subclusters. |
| 3.2 | 2026-03-07 | Agent | Cluster 2 opgesplitst in 2A/2B/2C; Cluster 4 uitgebreid met plan-vergelijking; Cluster 13 Journal & Reflection toegevoegd. |
| 3.1 | 2026-03-07 | Agent | Cluster 1 uitgebreid met subsectie Externe contextdata: 16 open databronnen. |
| 3.0 | 2026-03-07 | Agent | Volledig herschreven naar tabelformaat per cluster; alle 12 clusters; feature-beschrijvingen teruggebracht naar één zin. |
| 2.0 | 2026-03-07 | Agent | Compact restructure, cluster index, 4 nieuwe clusters (9–12). |
| 1.0 | 2026-03-07 | Agent | Initial draft. |
