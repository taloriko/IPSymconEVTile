# EV Tile

EV Tile ist ein Symcon-Gerätemodul zur nativen Darstellung vorhandener Fahrzeugdaten. Eine EV-Tile-Instanz verwendet genau eine Fahrzeug-Instanz als Quelle und erzeugt daraus auf Wunsch eine strukturierte Ansicht mit Links auf die Originalvariablen.

## 1. Referenzstand

Diese Version ist vollständig auf **MySkoda 1.5** abgestimmt.

Die automatische Zuordnung erwartet die aktuellen MySkoda-1.5-Idents und -Variablentypen. Eine Abwärtskompatibilität zu älteren MySkoda-Variablentypen ist ausdrücklich nicht vorgesehen.

Aktuell sind **73 Variablen** im Mapping definiert. Dazu gehören direkte API-Daten, von MySkoda erzeugte Modulwerte, optionale FIN-Daten und die zusätzlichen API-Fahrzeugdaten.

## 2. Konzept

EV Tile:

- führt keine Fahrzeug- oder Cloud-Abfragen aus,
- erstellt keine Spiegelvariablen,
- kopiert keine Fahrzeugwerte,
- ordnet Variablen über exakte technische Idents zu,
- prüft den erwarteten Variablentyp,
- erlaubt eine manuelle Zuordnung je Datenpunkt,
- verlinkt die Originalvariablen in eine native Symcon-Objektstruktur.

Dadurch bleiben Darstellung, Icon, Profil und vorhandene Aktionen der MySkoda-Variablen erhalten.

## 3. Voraussetzungen

- Symcon **8.1 oder neuer**
- MySkoda **1.5**
- eine konfigurierte MySkoda-Instanz als Datenquelle
- optional aktivierte MySkoda-Detail-/Diagnosevariablen
- optional aktivierte FIN-Informationsvariablen
- Archive Control mit Logging der Diagrammvariablen, wenn das Ladediagramm genutzt wird

## 4. Einrichtung

1. EV-Tile-Instanz anlegen.
2. Unter **Datenquelle** die MySkoda-Instanz auswählen.
3. Konfiguration übernehmen.
4. Den **Zuordnungsstatus** prüfen.
5. Bei Bedarf fehlende Datenpunkte manuell zuordnen.
6. **Gruppierte Fahrzeugansicht mit Links anlegen** aktivieren.
7. Optional das **Ladediagramm** aktivieren.

## 5. Zuordnungsstatus

Die Konfiguration zeigt kompakt:

- automatisch erkannt
- manuell zugeordnet
- fehlt

Ein automatischer Treffer wird nur verwendet, wenn:

1. der technische Ident exakt stimmt,
2. der Variablentyp dem MySkoda-1.5-Modell entspricht,
3. der Treffer eindeutig ist.

## 6. Native Objektstruktur

Die Struktur wird in folgender Reihenfolge erzeugt:

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

Nicht vorhandene Bereiche werden nicht mit leeren Links gefüllt.

### 6.1 Übersicht

**Übersicht** ist die kompakte Zusammenfassung für die Kachelvisualisierung. Die Reihenfolge ist bewusst nach Relevanz und nicht nach API-Struktur gewählt:

1. Ladezustand
2. Reichweite
3. Laden
4. Ladestatus
5. Ladeleistung
6. Ladelimit
7. Restladezeit
8. Klimatisierung
9. Klimatisierungsstatus
10. Solltemperatur
11. Kilometerstand
12. zuverlässiger Verriegelungsstatus
13. Türen
14. Fenster
15. Parkstatus
16. letzte Aktualisierung
17. Fahrzeugname
18. Kennzeichen

Fehlt ein Datenpunkt in der MySkoda-Instanz, wird er in der Übersicht ausgelassen.

### 6.2 API-Reihenfolge

Nach der Übersicht folgen alle direkt aus der Fahrzeugantwort abgeleiteten Werte in derselben fachlichen Reihenfolge wie die MySkoda-API-/Kompatibilitätsdokumentation:

#### Fahrzeug – `vehicle`

- `VehicleName`
- `LicensePlate`
- `VIN`

#### Klimatisierung – `vehicle.airConditioning`

- `ClimateState`
- `AirConditioningAtUnlock`
- `TargetTemperature`
- `TargetTemperatureUnit`
- `WindowHeatingEnabled`
- `WindowHeatingFront`
- `WindowHeatingRear`

#### Laden – `vehicle.charging`

- `AtSavedChargingLocation`
- `AutoUnlockPlug`
- `BatteryCareTargetSOC`
- `BatteryCareMode`
- `MaxChargeCurrentAC`
- `ChargeMode`
- `TargetSOC`
- `Range`
- `StateOfCharge`
- `ChargePower`
- `FullyChargedAt`
- `RemainingChargingTime`
- `ChargingState`
- `ChargeType`

#### Kilometerstand – `vehicle.odometer`

- `Mileage`

#### Parkposition – `vehicle.parkingPosition`

- `ParkingState`
- `ParkingAddress`
- `Latitude`
- `Longitude`

#### Fahrzeugstatus – `vehicle.status.overall`

- `DoorsLocked`
- `Locked`
- `DoorsOpen`
- `WindowsOpen`
- `LightsOn`
- `ReliableLockStatus`

#### Fahrzeugdetails – `vehicle.status.detail`

- `SunroofOpen`
- `TrunkOpen`
- `BonnetOpen`

Zeitstempel oder API-Strukturen ohne eigene Symcon-Variable erhalten keinen künstlichen Platzhalter.

## 7. Moduldaten

Nach den API-Daten folgen die von MySkoda selbst erzeugten Bedien- und Diagnosewerte:

- `Climate`
- `Charging`
- `LastUpdate`
- `ApiKeyWarning`
- `ApiKeyExpiresAtVar`
- `RequestsRemaining`
- `PartialErrors`
- `NewApiFeatures`
- `PendingCommands`
- `CommandStatus`

## 8. FIN-Daten

Wenn die FIN-Informationsvariablen in MySkoda angelegt wurden, übernimmt EV Tile anschließend:

- `VINWMI`
- `VINVDS`
- `VINVIS`
- `VINManufacturer`
- `VINCountry`
- `VINModel`
- `VINModelCode`
- `VINBody`
- `VINSteering`
- `VINDrive`
- `VINPower`
- `VINVariant`
- `VINRestraint`
- `VINModelYear`
- `VINPlant`
- `VINSerialNumber`
- `VINCheckDigit`

## 9. API-Fahrzeugdaten

Danach folgen die von MySkoda aus der Public-API-Antwort abgeleiteten Zusatzinformationen:

- `APICarType`
- `APIPrimaryEngineType`
- `APISecondaryEngineType`
- `APISupportedFeatures`
- `APIAvailableChargeModes`
- `APIRemoteOperations`
- `APIAuxiliaryHeatingState`
- `APIActiveVentilationState`

## 10. Typänderungen gegenüber älteren MySkoda-Ständen

Folgende Statusvariablen werden ausschließlich im aktuellen MySkoda-1.5-Typ **String** akzeptiert:

- `Locked`
- `DoorsOpen`
- `WindowsOpen`
- `TrunkOpen`
- `BonnetOpen`
- `SunroofOpen`
- `LightsOn`

Auch `DoorsLocked` und `ReliableLockStatus` sind String-Statuswerte.

Es existiert kein Boolean-Fallback für ältere Versionen.

## 11. Manuelle Variablenzuordnung

Für jeden Datenpunkt kann eine Variable manuell gewählt werden. Die manuelle Auswahl überschreibt die automatische Zuordnung nur für diesen Datenpunkt.

Ist die Auswahl ungültig oder vom falschen Typ, verwendet EV Tile wieder die automatische Zuordnung.

## 12. Kachelvisualisierung

EV Tile verwendet `SetVisualizationType(0)` und besitzt keine eigene HTML-Visualisierung.

Für die kompakte Fahrzeugansicht:

1. EV Tile in die Kachelvisualisierung aufnehmen.
2. **Einzelnes Element** auswählen.
3. **Übersicht** als Element wählen.

## 13. Ladediagramm

Optional wird unter **Diagramme** ein natives Symcon-Diagramm **Ladeübersicht** erzeugt:

- `StateOfCharge` – linke Achse
- `TargetSOC` – linke Achse
- `ChargePower` – rechte Achse

Alle drei Variablen müssen vorhanden sein. EV Tile verändert die Archive-Control-Konfiguration nicht.

## 14. Objekt- und Referenzverhalten

Die Quellinstanz und alle verwendeten Variablen werden mit `RegisterReference()` referenziert.

EV Tile erzeugt ausschließlich Gruppen, Links und optional ein Diagramm. Es verändert keine Properties der MySkoda-Instanz und führt dort kein `IPS_ApplyChanges()` aus.

Die Positionen der von EV Tile verwalteten Gruppen und Links werden beim Anwenden der Konfiguration entsprechend der aktuellen Struktur gesetzt.

## 15. Instanzstatus

| Code | Bedeutung |
|---:|---|
| `102` | Fahrzeugdaten zugeordnet |
| `104` | keine Fahrzeug-Instanz ausgewählt |
| `201` | ausgewählte Fahrzeug-Instanz existiert nicht |
| `202` | keine unterstützten Fahrzeugvariablen gefunden |

## 16. Datenschutz und externe Dienste

EV Tile führt keine externen Netzwerkaufrufe durch. Alle Daten stammen aus der lokalen Symcon-Installation.

## 17. Lizenz

Copyright © 2026 **taloriko**.

Veröffentlicht unter der [MIT-Lizenz](../LICENSE).
