# EV Tile für Symcon

EV Tile stellt bereits vorhandene Fahrzeugdaten mit nativen Symcon-Objekten übersichtlich dar. Das Modul führt selbst keine Fahrzeug- oder Cloud-Abfragen aus, sondern verwendet eine Fahrzeug-Instanz als Datenquelle und verlinkt deren Originalvariablen.

**Release 1.1 ist auf den aktuellen Variablenstand von MySkoda 1.5 abgestimmt.** Die automatische Zuordnung verwendet ausschließlich die aktuellen technischen Idents und Variablentypen von MySkoda 1.5. Ältere MySkoda-Datentypen werden nicht unterstützt.

## Funktionen

- automatische Zuordnung über den exakten technischen `Ident`
- strikte Typprüfung nach MySkoda 1.5
- **73 unterstützte Variablen** aus dem aktuellen MySkoda-1.5-Modell
- manuelle Zuordnung als Fallback je Datenpunkt
- kompakter Zuordnungsstatus: automatisch erkannt / manuell zugeordnet / fehlt
- native Gruppeninstanzen und Links auf die Originalvariablen
- keine Spiegelvariablen und keine kopierten Fahrzeugwerte
- eigene Gruppe **Übersicht** mit den wichtigsten Werten zuerst
- Detailstruktur in derselben Reihenfolge wie die MySkoda-API-Antwort
- danach getrennt **Moduldaten**, **FIN-Daten** und **API-Fahrzeugdaten**
- optionales natives Ladediagramm
- keine eigene HTML-Visualisierung
- keine externen Netzwerkaufrufe durch EV Tile

## Voraussetzungen

- Symcon **8.1 oder neuer**
- MySkoda **1.5** als Referenz-Datenquelle
- für FIN-Daten: in MySkoda aktivierte FIN-Informationsvariablen
- für Detailwerte: in MySkoda aktivierte Detail-/Diagnosevariablen, soweit erforderlich
- für das Ladediagramm: archivierte Quellvariablen im Archive Control

## Installation

Repository im **Module Control** hinzufügen:

```text
https://github.com/taloriko/IPSymconEVTile
```

Anschließend eine Instanz **EV Tile** anlegen und unter **Datenquelle** die MySkoda-Instanz auswählen.

## Objektstruktur

Bei aktivierter gruppierter Fahrzeugansicht wird die Struktur in dieser Reihenfolge angelegt:

```text
EV Tile
├── Übersicht
├── Fahrzeug
├── Klimatisierung
├── Laden
├── Kilometerstand
├── Parkposition
├── Fahrzeugstatus
├── Fahrzeugdetails
├── Moduldaten
├── FIN-Daten
├── API-Fahrzeugdaten
└── Diagramme
```

Die API-bezogenen Gruppen folgen der Struktur der Fahrzeugantwort:

1. `vehicle`
2. `vehicle.airConditioning`
3. `vehicle.charging`
4. `vehicle.odometer`
5. `vehicle.parkingPosition`
6. `vehicle.status.overall`
7. `vehicle.status.detail`

API-Bereiche ohne eigene Symcon-Variable, etwa reine Zeitstempel, Profile oder Operationslisten, erzeugen keine zusätzlichen Platzhalter.

### Übersicht

Die Gruppe **Übersicht** ist für die Kachelvisualisierung gedacht. Die wichtigsten Werte stehen bewusst zuerst:

`StateOfCharge` → `Range` → `Charging` → `ChargingState` → `ChargePower` → `TargetSOC` → `RemainingChargingTime` → `Climate` → `ClimateState` → `TargetTemperature` → `Mileage` → Verriegelungs-/Türstatus → `ParkingState` → `LastUpdate`.

Fahrzeugname und Kennzeichen folgen am Ende der Zusammenfassung.

## MySkoda-1.5-Datenmodell

EV Tile kennt den vollständigen aktuellen Variablensatz aus MySkoda 1.5:

- direkte Fahrzeug-/API-Daten
- von MySkoda erzeugte Bedien- und Diagnosevariablen
- lokal entschlüsselte FIN-Variablen
- von MySkoda erzeugte API-Fahrzeugdaten

Die Statuswerte `Locked`, `DoorsOpen`, `WindowsOpen`, `TrunkOpen`, `BonnetOpen`, `SunroofOpen` und `LightsOn` werden entsprechend MySkoda 1.5 als **String** erwartet. Eine Kompatibilität zu den früheren Boolean-Typen ist nicht vorgesehen.

## Kachelvisualisierung

EV Tile besitzt bewusst keine eigene HTML-Kachel. In der Symcon-Kachelvisualisierung:

1. EV Tile hinzufügen.
2. **Einzelnes Element** wählen.
3. **Übersicht** auswählen.

Dadurch wird die kompakte Zusammenfassung dargestellt.

## Ladediagramm

Optional kann EV Tile ein natives Symcon-Liniendiagramm **Ladeübersicht** anlegen:

- `StateOfCharge` – Ladezustand, linke Achse
- `TargetSOC` – Ladelimit, linke Achse
- `ChargePower` – Ladeleistung, rechte Achse

EV Tile verändert die Archivierung nicht. Die drei Quellvariablen müssen für einen Verlauf bereits im Archive Control geloggt werden.

## Zuordnung und Links

Die automatische Zuordnung basiert ausschließlich auf technischem Ident und passendem Variablentyp. Manuelle Zuordnungen überschreiben nur den jeweiligen Datenpunkt.

Alle Einträge der Fahrzeugansicht sind Links auf die Originalvariablen. Name, Icon, Darstellung und vorhandene Aktionen stammen damit direkt aus MySkoda.

## Datenschutz

EV Tile kommuniziert mit keinem externen Dienst. Es arbeitet ausschließlich mit Objekten der lokalen Symcon-Installation.

## Dokumentation

Die ausführliche Modul-Dokumentation befindet sich unter [EVTile/README.md](EVTile/README.md).

## Lizenz

Copyright © 2026 **taloriko**.

Veröffentlicht unter der [MIT-Lizenz](LICENSE).
