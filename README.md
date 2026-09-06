# EV Tile für IP-Symcon

EV Tile ist ein herstellerunabhängiges Visualisierungsmodul für Fahrzeugdaten in IP-Symcon. Das Modul liest vorhandene Fahrzeugvariablen aus einer frei wählbaren IP-Symcon-Instanz und stellt die wichtigsten Werte in **einer einzigen responsiven Kachel** dar.

**MySkoda** ist in Version 1.0 die erste vollständig unterstützte Datenquelle. Die Architektur ist bewusst offen aufgebaut, damit später weitere Fahrzeugmodule ergänzt werden können, ohne die Kachel selbst neu zu entwickeln.

## Funktionen

- frei wählbare Fahrzeug-Instanz als Datenquelle
- automatische Zuordnung ausschließlich über den **exakten technischen Ident**
- zusätzliche Prüfung des erwarteten Variablentyps
- keine automatische Erkennung über frei änderbare Variablennamen
- manuelle Zuordnung als Fallback für jeden einzelnen Datenpunkt
- Zuordnungsstatus mit **automatisch erkannt / manuell zugeordnet / fehlt**
- detaillierte Auflistung aller unterstützten Datenpunkte in der Instanzkonfiguration
- optionale gruppierte Listenansicht mit Dummy-Instanzen und Links auf die **Originalvariablen**
- responsive HTML-SDK-Kachel für Smartphone, Tablet und Desktop
- Light- und Dark-Mode
- Anzeige von Ladezustand, Reichweite, Kilometerstand, Fahrzeugstatus, Laden und Klima
- Diagnosehinweise für API-Key-Warnung, Teilfehler und neue API-Funktionen, sofern die Datenquelle diese liefert
- keine zyklische Abfrage durch EV Tile; Änderungen werden über IP-Symcon-Nachrichten übernommen
- Version 1.0 ist bewusst **Anzeige-only** und sendet keine Fahrzeugbefehle

## Voraussetzungen

- IP-Symcon **8.1 oder neuer**
- Kachelvisualisierung
- Fahrzeugdaten als IP-Symcon-Variablen unter einer auswählbaren Quellinstanz

Für die erste vollständig unterstützte Quelle siehe [MySkoda](https://github.com/taloriko/IPSymconMySkoda).

## Installation

Repository im **Module Control** hinzufügen:

```text
https://github.com/taloriko/IPSymconEVTile
```

Anschließend eine Instanz **EV Tile** anlegen.

## Erste Einrichtung

1. In der EV-Tile-Instanz unter **Datenquelle** die Fahrzeug-Instanz auswählen.
2. Konfiguration übernehmen.
3. Im Bereich **Zuordnungsstatus** prüfen, welche Datenpunkte automatisch erkannt wurden.
4. Fehlende Datenpunkte bei Bedarf unter **Manuelle Variablenzuordnung** ergänzen.
5. Optional **Gruppierte Listenansicht mit Links anlegen** aktivieren und erneut übernehmen.
6. Die EV-Tile-Instanz als einzelnes Element in die Kachelvisualisierung aufnehmen.

## Zuordnung

Die automatische Zuordnung erfolgt ausschließlich über den technischen `Ident` einer Variable. Ein Treffer wird nur verwendet, wenn der Variablentyp zur erwarteten Rolle passt und der Treffer eindeutig ist.

Beispiel:

```text
StateOfCharge -> Ladezustand
Range         -> Reichweite
Mileage       -> Kilometerstand
Locked        -> Verriegelt
```

Eine manuelle Zuordnung überschreibt nur den jeweiligen Datenpunkt. Ist eine manuell ausgewählte Variable später nicht mehr vorhanden oder besitzt den falschen Typ, fällt EV Tile auf die automatische Ident-Zuordnung zurück.

## Listenansicht

Die Listenansicht ist **standardmäßig deaktiviert**, da hierfür zusätzliche Objekte angelegt werden. Erst nach ausdrücklicher Aktivierung erzeugt EV Tile unterhalb seiner Instanz eigene Gruppen und Links:

```text
EV Tile
├── Fahrzeug
├── Status
├── Laden
├── Klima
├── Standort
└── Diagnose
```

Die Links zeigen direkt auf die Originalvariablen. Es werden keine Fahrzeugwerte kopiert und keine Spiegelvariablen angelegt. Namen, Icons und Positionen der erzeugten Objekte werden nur bei ihrer Erstanlage gesetzt und später nicht überschrieben.

## Kachel

Die Kachel ist ein einziges HTML-SDK-Element. Sie zeigt abhängig von den tatsächlich verfügbaren Daten unter anderem:

- Fahrzeugname und Kennzeichen
- Ladezustand mit 20-Segment-Batterieanzeige
- Reichweite und Kilometerstand
- Verriegelung sowie offene Türen, Fenster, Kofferraum, Motorhaube und Schiebedach
- Lichtstatus
- Ladestatus, Ladeleistung, Ladelimit, Ladeart, Lademodus und erwarteten Ladeabschluss
- Klimatisierung und Solltemperatur
- relevante Diagnosehinweise

Fehlende Werte werden ausgeblendet. Die Kachel bleibt dadurch auch bei einer Datenquelle mit nur wenigen Fahrzeugwerten sinnvoll nutzbar.

## Herstellerunabhängigkeit

Die Visualisierung arbeitet intern mit einem normalisierten Fahrzeugzustand. Nur die Mapping-Schicht kennt konkrete Quell-Idents. Dadurch kann die Unterstützung weiterer Fahrzeugmodule später ergänzt werden, ohne das HTML-Layout an einen Hersteller zu koppeln.

In Version 1.0 ist MySkoda das Referenzprofil und die erste vollständig unterstützte Referenzquelle.

## Datenschutz

EV Tile führt selbst **keine externen Netzwerkaufrufe** durch. Es verarbeitet ausschließlich Daten aus der lokalen IP-Symcon-Installation und referenziert die ausgewählten Fahrzeugvariablen.

## Dokumentation

Die vollständige Modul-Dokumentation befindet sich unter [EVTile/README.md](EVTile/README.md).

## Fehler melden

Fehler und nachvollziehbare Verbesserungsvorschläge können über die [GitHub Issues](https://github.com/taloriko/IPSymconEVTile/issues) gemeldet werden.

## Lizenz

Copyright © 2026 **taloriko**.

Veröffentlicht unter der [MIT-Lizenz](LICENSE).
