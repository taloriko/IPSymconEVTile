# EV Tile für IP-Symcon

Herstellerunabhängige Fahrzeug-Kachel für die IP-Symcon Kachelvisualisierung.

EV Tile stellt typische Daten eines Elektrofahrzeugs in einer kompakten, responsiven Kachel dar. Die Datenquelle ist frei wählbar; das Modul ist nicht an einen bestimmten Fahrzeughersteller oder ein bestimmtes API-Modul gebunden.

## Funktionsumfang

- Ladezustand als integrierter 20-Segment-Balken im Ladebereich
- Reichweite und Kilometerstand
- Verriegelungsstatus
- Türen, Fenster, Kofferraum, Motorhaube, Schiebedach und Licht
- Ladezustand, Ladeleistung, Ladelimit und Lademodus
- Ladeart und erwarteter Ladeabschluss
- Klimatisierung und Solltemperatur
- letzte Aktualisierung
- optionale Bedienung von Laden und Klimatisierung über vorhandene Variablenaktionen
- automatische Zuordnung bekannter Variablen-Idents und Variablennamen
- kompakter Zuordnungsstatus in der Instanzkonfiguration
- manuelle Variablenzuordnung für nicht automatisch erkannte Datenpunkte
- responsive Darstellung für Smartphone und Desktop

Diagnose-, API- und Hersteller-spezifische Verwaltungsdaten sind nicht Bestandteil der Kachel.

## Voraussetzungen

- IP-Symcon 8.1 oder neuer
- Kachelvisualisierung
- Fahrzeugdaten als Symcon-Variablen

## Installation

Repository im **Module Control** hinzufügen:

```text
https://github.com/taloriko/IPSymconEVTile
```

Anschließend eine Instanz **EV Tile** anlegen.

## Konfiguration

Zuerst wird die Fahrzeug- bzw. Quellinstanz ausgewählt. Nach **Übernehmen** wertet das Modul die untergeordneten Variablen aus und ordnet bekannte Datenpunkte automatisch zu.

Der Bereich **Zuordnungsstatus** zeigt nur die Anzahl der Zuordnungen:

- 🟢 **Automatisch erkannt** – Anzahl automatisch gefundener Datenpunkte
- 🔵 **Manuell zugeordnet** – Anzahl manuell überschriebener Datenpunkte
- 🟠 **Fehlt** – Anzahl noch nicht zugeordneter Datenpunkte

In der **Manuellen Variablenzuordnung** wird direkt am jeweiligen Feld angezeigt, welche Variable die automatische Erkennung gefunden hat. Beispiel:

```text
🟢 Ladezustand (Auto #12345)
🔵 Kennzeichen (Auto #12346)
🟠 Ladeart (Auto —)
```

Eine manuelle Auswahl überschreibt ausschließlich den jeweiligen Datenpunkt. Die übrigen Zuordnungen bleiben automatisch.

Unterstützte Standard-Idents sind unter anderem:

```text
StateOfCharge
Range
Mileage
Locked
DoorsOpen
WindowsOpen
Charging
ChargePower
TargetSOC
ChargeMode
Climate
TargetTemperature
VehicleName
LicensePlate
ChargingState
ChargeType
FullyChargedAt
TrunkOpen
BonnetOpen
SunroofOpen
LightsOn
ParkingState
LastUpdate
```

Für die automatische Erkennung werden zusätzlich typische deutsche und englische Variablennamen berücksichtigt. Damit können auch Fahrzeugmodule ohne identische Idents automatisch erkannt werden.

## Bedienung

Besitzt die zugeordnete Variable eine Symcon-Aktion, kann die Kachel unterstützte Funktionen wie Laden oder Klimatisierung direkt auslösen. Bedienbare Funktionen werden über eigene kleine Aktionsschaltflächen angezeigt. Die übrigen Kachelflächen sind reine Anzeigeelemente und lösen bei Berührung keine Fahrzeugaktion aus.

Die Bedienung kann in der Konfiguration vollständig deaktiviert werden.

## Darstellung

Die Kachel verwendet eine native HTML-Kachel über das Symcon Tile SDK. Änderungen der Quellvariablen werden über Symcon-Nachrichten überwacht und ohne zyklisches Polling an die Kachel übertragen.

Beim Erzeugen der Kachel werden die Variablenzuordnungen erneut direkt aus der aktuellen Konfiguration und der Quellinstanz aufgelöst. Dadurch ist die Initialdarstellung nicht von einem zuvor gespeicherten Zuordnungszustand abhängig.

Die Fahrzeugdarstellung ist herstellerneutral und quer ausgerichtet, damit auf Smartphones weniger vertikaler Platz benötigt wird. Der Ladezustand wird als eigene, in den Ladeblock integrierte Zeile dargestellt. Der Balken besteht aus 20 Segmenten und bildet damit Schritte von jeweils 5 Prozent ab. Die Segmentfarbe folgt dem Ladezustand von Rot über Orange und Hellgrün bis Dunkelgrün.

Zustände wie offene Türen, Fenster, Kofferraum, Motorhaube, Schiebedach oder eingeschaltetes Licht werden direkt am Fahrzeug hervorgehoben.

Eingehende Visualisierungsnachrichten werden mit dem zuletzt bekannten vollständigen Fahrzeugzustand zusammengeführt. Teilnachrichten bei Bedienaktionen können dadurch nicht die übrigen Anzeigen leeren.

## Architekturhinweis

Die technische Umsetzung orientiert sich an den öffentlich dokumentierten Architekturmustern der TileVisu-Module von da8ter, insbesondere an der Nutzung von `SetVisualizationType(1)`, `GetVisualizationTile()`, `MessageSink()` und `UpdateVisualizationValue()`. Quellcode und grafische Assets wurden eigenständig erstellt.

## Lizenz

Copyright © 2026 taloriko

Veröffentlicht unter der MIT-Lizenz. Siehe [LICENSE](LICENSE).
