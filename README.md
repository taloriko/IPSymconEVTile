# EV Tile für IP-Symcon

EV Tile ist ein herstellerunabhängiges IP-Symcon-Modul zur übersichtlichen Darstellung bereits vorhandener Fahrzeugdaten mit **nativen Symcon-Objekten**.

Das Modul führt selbst keine Fahrzeug- oder Cloud-Abfragen durch. Es verwendet eine frei wählbare Fahrzeug-Instanz als Datenquelle, ordnet deren Variablen über stabile technische Idents zu und kann daraus eine strukturierte Fahrzeugansicht mit Links auf die Originalvariablen erzeugen.

**MySkoda** ist in Version 1.0 die erste vollständig unterstützte Referenzquelle. Die Architektur bleibt offen für weitere Fahrzeugmodule und Hersteller.

## Funktionen

- frei wählbare Fahrzeug-Instanz als Datenquelle
- automatische Zuordnung ausschließlich über den **exakten technischen Ident**
- Prüfung des erwarteten Variablentyps
- keine Erkennung über benutzeränderbare Variablennamen
- manuelle Zuordnung als Fallback für jeden Datenpunkt
- kompakter Zuordnungsstatus: **automatisch erkannt / manuell zugeordnet / fehlt**
- 30 definierte MySkoda-Datenpunkte als Referenzmodell
- optionale native Objektstruktur mit Gruppeninstanzen und Links auf die **Originalvariablen**
- Gruppe **Fahrzeug** als kompakte Übersicht der wichtigsten Werte
- Detailgruppen **Status**, **Laden**, **Klima**, **Standort** und **Diagnose**
- optionale Gruppe **Diagramme** mit einem nativen Ladediagramm
- Ladediagramm: Ladezustand und Ladelimit links, Ladeleistung rechts
- keine Spiegelvariablen und keine kopierten Fahrzeugwerte
- keine eigene HTML-Visualisierung
- keine externen Netzwerkaufrufe durch EV Tile

## Voraussetzungen

- IP-Symcon **8.1 oder neuer**
- Fahrzeugdaten als IP-Symcon-Variablen unter einer auswählbaren Quellinstanz
- für das optionale Ladediagramm: Archivierung der verwendeten Quellvariablen im Archive Control

Als erste vollständig unterstützte Quelle dient [MySkoda](https://github.com/taloriko/IPSymconMySkoda).

## Installation

Repository im **Module Control** hinzufügen:

```text
https://github.com/taloriko/IPSymconEVTile
```

Anschließend eine Instanz **EV Tile** anlegen.

## Erste Einrichtung

1. Unter **Datenquelle** die Fahrzeug-Instanz auswählen.
2. Konfiguration übernehmen.
3. Im **Zuordnungsstatus** die Anzahl automatisch erkannter, manuell zugeordneter und fehlender Datenpunkte prüfen.
4. Fehlende Werte bei Bedarf unter **Manuelle Variablenzuordnung** ergänzen.
5. Bei Bedarf **Gruppierte Fahrzeugansicht mit Links anlegen** aktivieren.
6. Optional **Ladediagramm anlegen** aktivieren.

Die zusätzlichen Gruppen, Links und das Diagramm werden nur nach ausdrücklicher Aktivierung angelegt.

## Objektstruktur

Bei aktivierter Fahrzeugansicht entsteht abhängig von den verfügbaren Daten ungefähr folgende Struktur:

```text
EV Tile
├── Fahrzeug
│   ├── Fahrzeugname
│   ├── Kennzeichen
│   ├── Ladezustand
│   ├── Reichweite
│   ├── Kilometerstand
│   ├── Verriegelt
│   ├── Laden
│   ├── Ladeleistung
│   ├── Ladelimit
│   ├── Klimatisierung
│   └── ...
├── Status
├── Laden
├── Klima
├── Standort
├── Diagnose
└── Diagramme
    └── Ladeübersicht
```

Die Einträge unter den Gruppen sind **Links auf die Originalvariablen**. EV Tile vergibt den Links keinen eigenen Anzeigenamen und kein eigenes Icon. Dadurch werden Name, Darstellung, Profil, Icon und vorhandene Aktionen der Originalvariable verwendet.

Nur die von EV Tile selbst angelegten Gruppeninstanzen erhalten bei der Erstanlage einen sinnvollen Namen, ein Icon und eine Position. Bereits bestehende Benutzeranpassungen werden bei späteren `ApplyChanges()` nicht überschrieben.

## Kachelvisualisierung

EV Tile besitzt bewusst **keine eigene HTML-Kachel**. Die native Objektstruktur wird direkt von der IP-Symcon Kachelvisualisierung dargestellt.

Für eine kompakte Fahrzeugansicht wird EV Tile in der Kachelvisualisierung als **Einzelnes Element** eingebunden und dort **Fahrzeug** ausgewählt.

Diese Auswahl gehört zur Konfiguration der Kachelvisualisierung und wird von Symcon dort gespeichert. Das PHP-Modul kann die Auswahl **Einzelnes Element → Fahrzeug** daher nicht zuverlässig vorgeben; sie wird einmalig in der Visualisierung eingestellt.

## Ladediagramm

Optional kann EV Tile unter **Diagramme** ein natives Symcon-Liniendiagramm **Ladeübersicht** anlegen:

- `StateOfCharge` – Ladezustand, linke Achse
- `TargetSOC` – Ladelimit, linke Achse
- `ChargePower` – Ladeleistung, rechte Achse

Das Diagramm wird nur angelegt, wenn alle drei Datenpunkte verfügbar sind. EV Tile aktiviert **keine Archivierung selbstständig**. Für einen zeitlichen Verlauf müssen die Quellvariablen bereits im Archive Control geloggt werden. Beim MySkoda-Modul kann dies dort ausdrücklich aktiviert werden.

Nach der Erstanlage bleibt die Diagrammkonfiguration benutzereigen und wird von EV Tile nicht bei jedem Anwenden überschrieben.

## Zuordnung

Die automatische Zuordnung verwendet ausschließlich den technischen `Ident` einer Variable. Ein Treffer wird nur übernommen, wenn der Variablentyp passt und der Treffer eindeutig ist.

Beispiele:

```text
StateOfCharge -> Ladezustand
Range         -> Reichweite
Mileage       -> Kilometerstand
Locked        -> Verriegelt
```

Eine manuelle Zuordnung überschreibt nur den jeweiligen Datenpunkt. Ist die manuell gewählte Variable später nicht mehr vorhanden oder vom falschen Typ, fällt EV Tile auf die automatische Ident-Zuordnung zurück.

## Herstellerunabhängigkeit

Die Zuordnungsschicht trennt die fachlichen Fahrzeugrollen von der jeweiligen Datenquelle. Version 1.0 definiert das vollständige MySkoda-Mapping; weitere Hersteller oder Fahrzeugmodule können später ergänzt werden, ohne die grundlegende Objektstruktur neu aufzubauen.

## Datenschutz

EV Tile führt **keine externen Netzwerkaufrufe** durch. Es verarbeitet ausschließlich Objekte und Werte der lokalen IP-Symcon-Installation. Die Kommunikation mit einem Fahrzeughersteller bleibt Aufgabe des jeweiligen Fahrzeugmoduls.

## Dokumentation

Die vollständige Modul-Dokumentation befindet sich unter [EVTile/README.md](EVTile/README.md).

## Fehler melden

Fehler und Verbesserungsvorschläge können über die [GitHub Issues](https://github.com/taloriko/IPSymconEVTile/issues) gemeldet werden.

## Lizenz

Copyright © 2026 **taloriko**.

Veröffentlicht unter der [MIT-Lizenz](LICENSE).
