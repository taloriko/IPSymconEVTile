# EV Tile

Herstellerunabhängiges IP-Symcon-Gerätemodul zur Visualisierung bereits vorhandener Fahrzeugdaten. Eine EV-Tile-Instanz verwendet genau eine frei wählbare Fahrzeug-Instanz als Datenquelle.

## Konzept

EV Tile ist bewusst von Fahrzeug-APIs getrennt. Das Modul führt keine eigenen Hersteller- oder Cloud-Abfragen durch. Stattdessen werden vorhandene IP-Symcon-Variablen einer Quellinstanz anhand stabiler technischer Idents zugeordnet und in einen herstellerneutralen internen Fahrzeugzustand überführt.

**Version 1.0 unterstützt MySkoda vollständig als Referenz-Datenquelle.** Andere Fahrzeugmodule können bereits über die manuelle Variablenzuordnung verwendet werden. Weitere feste Mapping-Profile können später ergänzt werden, ohne die Visualisierung neu aufzubauen.

## Voraussetzungen

- IP-Symcon **8.1 oder neuer**
- Kachelvisualisierung
- eine Fahrzeug- oder Quellinstanz mit Fahrzeugdaten als IP-Symcon-Variablen

## Installation und erste Einrichtung

1. Repository `https://github.com/taloriko/IPSymconEVTile` im Module Control hinzufügen.
2. Instanz **EV Tile** anlegen.
3. Unter **Datenquelle** die Fahrzeug-Instanz auswählen.
4. Konfiguration übernehmen.
5. Den Bereich **Zuordnungsstatus** prüfen.
6. Fehlende Werte bei Bedarf unter **Manuelle Variablenzuordnung** ergänzen.
7. Optional die gruppierte Listenansicht aktivieren.
8. EV Tile als einzelnes Element in die Kachelvisualisierung aufnehmen.

Die Instanz kann auch ohne gewählte Datenquelle fehlerfrei angelegt werden und bleibt dann inaktiv.

## Automatische Zuordnung

Die automatische Zuordnung verwendet ausschließlich den **exakten technischen Objekt-Ident**.

Es werden ausdrücklich keine Variablennamen wie `Ladezustand`, `SOC`, `Battery` oder ähnliche freie Bezeichnungen ausgewertet. Namen sind benutzeränderbar und deshalb keine stabile Schnittstelle.

Für jeden Datenpunkt wird geprüft:

1. Existiert unter der gewählten Quellinstanz eine Variable mit exakt dem erwarteten Ident?
2. Entspricht ihr Variablentyp dem erwarteten Typ?
3. Ist der Treffer eindeutig?

Nur dann wird die Variable automatisch verwendet. Bei keinem Treffer, falschem Typ oder Mehrdeutigkeit bleibt der Datenpunkt unzugeordnet und kann manuell ausgewählt werden.

Die Suche umfasst die Quellinstanz und bis zu zwei darunterliegende Ebenen aus Kategorien oder Instanzen. Mehrere gültige Variablen mit demselben Ident werden nicht automatisch geraten.

## Manuelle Variablenzuordnung

Jeder Datenpunkt besitzt eine optionale manuelle `SelectVariable`-Zuordnung.

Priorität:

```text
manuelle Zuordnung > automatische Ident-Zuordnung
```

Ist eine manuelle Auswahl ungültig, nicht mehr vorhanden oder vom falschen Variablentyp, fällt EV Tile automatisch auf die Ident-Zuordnung zurück.

Eine manuelle Auswahl beeinflusst nur diesen einen Datenpunkt.

## Zuordnungsstatus

Direkt unter der Datenquelle zeigt die Instanzkonfiguration:

- 🟢 **Automatisch erkannt**
- 🔵 **Manuell zugeordnet**
- 🟠 **Fehlt**
- zusätzlich die Anzahl verfügbarer Datenpunkte insgesamt

Darunter wird jeder Datenpunkt mit Gruppe, Bezeichnung, erwartetem Ident und effektiv verwendeter Variable aufgeführt.

Beispiel:

```text
🟢 Ladezustand · StateOfCharge · #12345 Ladezustand
🔵 Kennzeichen · LicensePlate · #54321 Kennzeichen
🟠 Längengrad · Longitude · fehlt
```

Bei Mehrdeutigkeit oder einem falschen Variablentyp wird dies in der Übersicht kenntlich gemacht.

## Unterstützte MySkoda-Datenpunkte in Version 1.0

Version 1.0 definiert **30 feste Datenpunkte** als kanonisches EV-Tile-Datenmodell für MySkoda.

| Gruppe | Ident | Anzeige | Erwarteter Typ |
|---|---|---|---|
| Fahrzeug | `VehicleName` | Fahrzeugname | String |
| Fahrzeug | `LicensePlate` | Kennzeichen | String |
| Fahrzeug | `Range` | Reichweite | Integer / Float |
| Fahrzeug | `Mileage` | Kilometerstand | Integer / Float |
| Fahrzeug | `ParkingState` | Parkstatus | String |
| Fahrzeug | `LastUpdate` | Letzte Aktualisierung | Integer |
| Status | `Locked` | Verriegelt | Boolean |
| Status | `DoorsOpen` | Türen offen | Boolean |
| Status | `WindowsOpen` | Fenster offen | Boolean |
| Status | `TrunkOpen` | Kofferraum offen | Boolean |
| Status | `BonnetOpen` | Motorhaube offen | Boolean |
| Status | `SunroofOpen` | Schiebedach offen | Boolean |
| Status | `LightsOn` | Licht an | Boolean |
| Laden | `StateOfCharge` | Ladezustand | Integer / Float |
| Laden | `Charging` | Laden | Boolean |
| Laden | `ChargingState` | Ladestatus | String |
| Laden | `ChargeType` | Ladeart | String |
| Laden | `ChargePower` | Ladeleistung | Integer / Float |
| Laden | `TargetSOC` | Ladelimit | Integer / Float |
| Laden | `ChargeMode` | Lademodus | Integer / String |
| Laden | `FullyChargedAt` | Vollgeladen um | Integer |
| Klima | `Climate` | Klimatisierung | Boolean |
| Klima | `TargetTemperature` | Solltemperatur | Integer / Float |
| Standort | `Latitude` | Breitengrad | Integer / Float |
| Standort | `Longitude` | Längengrad | Integer / Float |
| Diagnose | `ApiKeyWarning` | API-Key Warnung | Boolean |
| Diagnose | `ApiKeyExpiresAtVar` | API-Key gültig bis | Integer |
| Diagnose | `RequestsRemaining` | Verbleibende API-Anfragen | Integer |
| Diagnose | `PartialErrors` | API-Teilfehler | String |
| Diagnose | `NewApiFeatures` | Neue API-Funktionen | Integer |

## Gruppierte Listenansicht

Die Option **Gruppierte Listenansicht mit Links anlegen** ist standardmäßig **aus**.

Erst nach ausdrücklicher Aktivierung legt EV Tile eigene Dummy-Instanzen und Links unterhalb der EV-Tile-Instanz an:

```text
EV Tile
├── Fahrzeug
│   ├── Fahrzeugname      -> Originalvariable
│   ├── Kennzeichen       -> Originalvariable
│   ├── Reichweite        -> Originalvariable
│   └── ...
├── Status
├── Laden
├── Klima
├── Standort
└── Diagnose
```

Nur aktuell zugeordnete Datenpunkte werden beim Aufbau berücksichtigt. Die Links zeigen direkt auf die Originalvariablen, sodass deren Wertdarstellungen und vorhandene Aktionen erhalten bleiben.

EV Tile legt keine Spiegelvariablen an und kopiert keine Werte. Namen, Icons und Positionen der von EV Tile erzeugten Gruppen und Links werden nur bei ihrer Erstanlage gesetzt. Spätere Benutzeranpassungen an diesen Eigenschaften werden nicht überschrieben. Wenn sich die Datenzuordnung ändert und ein bereits vorhandener EV-Tile-Link weiterverwendet wird, darf nur dessen Zielvariable angepasst werden.

Vorhandene EV-Tile-Gruppen oder Links werden beim Deaktivieren der Option nicht automatisch gelöscht.

## Kachelvisualisierung

EV Tile verwendet das native IP-Symcon HTML-SDK:

- `SetVisualizationType(1)`
- `GetVisualizationTile()`
- `MessageSink()`
- `UpdateVisualizationValue()`
- `VM_UPDATE` für Änderungen der referenzierten Quellvariablen

Es gibt kein zyklisches Polling durch EV Tile. Änderungen an den zugeordneten Fahrzeugvariablen werden über IP-Symcon-Nachrichten an die Kachel weitergegeben.

Die gesamte Darstellung ist **ein einzelnes Kachel-Element**.

### Darstellung

Die Kachel ist herstellerneutral, responsiv und für Light- und Dark-Mode ausgelegt. Sie enthält abhängig von den verfügbaren Daten:

- Fahrzeugname, Kennzeichen und Aktualisierungszeit
- Ladezustand als große Prozentanzeige mit 20 Segmenten
- Reichweite und Kilometerstand
- grafische Fahrzeugdarstellung
- Verriegelung und relevante offene Fahrzeugteile
- Ladestatus, Ladeleistung, Ladelimit, Ladeart, Lademodus und Ladeende
- Klimatisierung und Solltemperatur
- relevante Diagnosehinweise

Fehlende Datenblöcke oder Einzelwerte werden automatisch ausgeblendet.

Die Batterieanzeige verwendet folgende Stufen:

- bis 10 %: kritisch
- bis 25 %: niedrig
- unter 80 %: normal
- ab 80 %: hoher Ladezustand

## Bedienung

Version 1.0 ist bewusst **Anzeige-only**. Die Kachel sendet keine Lade-, Klima- oder sonstigen Fahrzeugbefehle.

Die optionale Listenansicht verweist jedoch auf die Originalvariablen. Falls diese in der Datenquelle bereits Aktionen besitzen, bleiben diese Eigenschaften der Originalvariablen unverändert.

## Objekt- und Referenzverhalten

Die gewählte Quellinstanz und alle effektiv verwendeten Variablen werden mit `RegisterReference()` referenziert. Auf die verwendeten Variablen wird zusätzlich `VM_UPDATE` registriert.

EV Tile verändert keine Properties der Fahrzeug-Instanz und ruft kein `IPS_ApplyChanges()` auf fremden Instanzen auf.

## Instanzstatus

| Code | Bedeutung |
|---:|---|
| `102` | Fahrzeugdaten erfolgreich zugeordnet |
| `104` | keine Fahrzeug-Instanz ausgewählt |
| `201` | ausgewählte Fahrzeug-Instanz existiert nicht |
| `202` | keine unterstützten Fahrzeugvariablen gefunden |

## Fehlersuche

- **Automatisch erkannt: 0** – prüfen, ob die richtige Fahrzeug-Instanz ausgewählt wurde und die Quellvariablen die erwarteten Idents besitzen.
- **Fehlt trotz passendem Ident** – Variablentyp prüfen. EV Tile übernimmt keine Variable mit unpassendem Datentyp.
- **Mehrdeutig** – derselbe Ident wurde mehrfach gefunden. Den gewünschten Wert manuell zuordnen.
- **Kachel leer** – Datenquelle übernehmen und prüfen, ob mindestens ein unterstützter Datenpunkt verfügbar ist.
- **Listenansicht fehlt** – Option ausdrücklich aktivieren und die Konfiguration übernehmen.

## Datenschutz und externe Dienste

EV Tile kommuniziert mit **keinem externen Dienst**. Alle Daten stammen aus der lokalen IP-Symcon-Installation. Der Netzwerkzugriff auf einen Fahrzeughersteller ist Aufgabe des jeweils verwendeten Fahrzeugmoduls.

## Hilfe und Quellcode

- Repository: `https://github.com/taloriko/IPSymconEVTile`
- Dokumentation: `https://github.com/taloriko/IPSymconEVTile/blob/main/EVTile/README.md`
- Issues: `https://github.com/taloriko/IPSymconEVTile/issues`

## Lizenz

Copyright © 2026 **taloriko**.

Dieses Projekt wird unter der [MIT-Lizenz](../LICENSE) veröffentlicht.
