# Changelog

## 1.1 - 2026-09-15

Aktualisiert am 2026-09-20 auf den aktuellen Stand von **MySkoda 1.5**.

- Referenz-Mapping vollständig auf MySkoda 1.5 umgestellt
- 73 aktuelle MySkoda-Variablen unterstützt
- neue Public-API-, Diagnose-, FIN- und API-Fahrzeugvariablen ergänzt
- Statusvariablen wie `Locked`, `DoorsOpen`, `WindowsOpen`, `TrunkOpen`, `BonnetOpen`, `SunroofOpen` und `LightsOn` ausschließlich als aktuelle String-Typen
- keine Abwärtskompatibilität zu älteren MySkoda-Boolean-Typen
- neue Gruppe **Übersicht** mit den wichtigsten Werten zuerst
- Detailgruppen an die Reihenfolge der MySkoda-API-Antwort angepasst
- Reihenfolge danach: **Moduldaten → FIN-Daten → API-Fahrzeugdaten → Diagramme**
- Positionen verwalteter Gruppen und Links werden beim Anwenden der Konfiguration aktualisiert
- `PendingCommands` und `CommandStatus` weiterhin unterstützt
- keine HTML-Visualisierung, keine Spiegelvariablen und keine externen Netzwerkaufrufe

## 1.0 - 2026-09-06

- Initial release of EV Tile for Symcon
- manufacturer-independent native Symcon object architecture with MySkoda as the first fully supported data source
- automatic mapping by exact technical variable Ident with variable type validation
- compact assignment status with automatically detected, manually assigned and missing data points
- manual per-data-point fallback mapping
- optional grouped vehicle view with native group instances and links to the original vehicle variables
- Vehicle group as the compact overview for the Tile Visualization
- links keep the original variable name, icon, presentation and actions
- optional native charging chart with state of charge and charging limit on the left axis and charging power on the right axis
- no HTML visualization, no mirrored vehicle variables and no external network requests
