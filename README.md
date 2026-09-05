# EV Tile für IP-Symcon

Herstellerunabhängige Fahrzeug-Kachel für die IP-Symcon Kachelvisualisierung.

EV Tile stellt typische Daten eines Elektrofahrzeugs in einer kompakten, responsiven Kachel dar. Die Datenquelle ist frei wählbar; das Modul ist nicht an einen bestimmten Fahrzeughersteller oder ein bestimmtes API-Modul gebunden.

## Funktionsumfang

- Ladezustand mit Batterieanzeige
- Reichweite und Kilometerstand
- Verriegelungsstatus
- Türen, Fenster, Kofferraum, Motorhaube, Schiebedach und Licht
- Ladezustand, Ladeleistung, Ladelimit und Lademodus
- Ladeart und erwarteter Ladeabschluss
- Klimatisierung und Solltemperatur
- letzte Aktualisierung
- optionale Bedienung von Laden und Klimatisierung über vorhandene Variablenaktionen
- automatische Zuordnung bekannter Variablen-Idents
- manuelle Variablenzuordnung für andere Fahrzeugmodule
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

Zuerst wird die Fahrzeug- bzw. Quellinstanz ausgewählt. Das Modul versucht anschließend, bekannte Variablen über ihre Idents automatisch zuzuordnen.

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

Nicht automatisch erkannte Datenpunkte können manuell zugeordnet werden.

## Bedienung

Besitzt die zugeordnete Variable eine Symcon-Aktion, kann die Kachel unterstützte Funktionen wie Laden oder Klimatisierung direkt auslösen. Die Bedienung kann in der Konfiguration vollständig deaktiviert werden.

## Darstellung

Die Kachel verwendet eine native HTML-Kachel über das Symcon Tile SDK. Änderungen der Quellvariablen werden über Symcon-Nachrichten überwacht und ohne zyklisches Polling an die Kachel übertragen.

Die Fahrzeugdarstellung ist herstellerneutral. Zustände wie offene Türen, Fenster, Kofferraum, Motorhaube, Schiebedach oder eingeschaltetes Licht werden direkt am Fahrzeug hervorgehoben.

## Architekturhinweis

Die technische Umsetzung orientiert sich an den öffentlich dokumentierten Architekturmustern der TileVisu-Module von da8ter, insbesondere an der Nutzung von `SetVisualizationType(1)`, `GetVisualizationTile()`, `MessageSink()` und `UpdateVisualizationValue()`. Quellcode und grafische Assets wurden eigenständig erstellt.

## Lizenz

Copyright © 2026 taloriko

Veröffentlicht unter der MIT-Lizenz. Siehe [LICENSE](LICENSE).
