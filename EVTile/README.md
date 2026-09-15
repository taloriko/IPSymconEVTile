# EV Tile

Herstellerunabhängiges Symcon-Gerätemodul zur strukturierten Darstellung bereits vorhandener Fahrzeugdaten mit nativen Symcon-Objekten. Eine EV-Tile-Instanz verwendet genau eine frei wählbare Fahrzeug-Instanz als Datenquelle.

## Konzept

EV Tile ist bewusst von Fahrzeug-APIs getrennt. Das Modul führt keine eigenen Hersteller- oder Cloud-Abfragen durch. Stattdessen werden vorhandene Symcon-Variablen einer Quellinstanz anhand stabiler technischer Idents zugeordnet.

**Version 1.1 unterstützt MySkoda 1.1 vollständig als Referenz-Datenquelle.** Andere Fahrzeugmodule können über die manuelle Variablenzuordnung verwendet werden.

EV Tile verwendet **keine eigene HTML-Visualisierung**. Die Ausgabe besteht aus nativen Gruppeninstanzen, Links auf Originalvariablen und optional einem nativen Symcon-Diagramm.

## Voraussetzungen

- Symcon **8.1 oder neuer**
- eine Fahrzeug- oder Quellinstanz mit Fahrzeugdaten als Symcon-Variablen
- für das optionale Ladediagramm: die verwendeten Quellvariablen müssen im Archive Control geloggt werden

## Installation und erste Einrichtung

1. Repository `https://github.com/taloriko/IPSymconEVTile` im Module Control hinzufügen.
2. Instanz **EV Tile** anlegen.
3. Unter **Datenquelle** die Fahrzeug-Instanz auswählen.
4. Konfiguration übernehmen.
5. Den kompakten **Zuordnungsstatus** prüfen.
6. Fehlende Werte bei Bedarf unter **Manuelle Variablenzuordnung** ergänzen.
7. Optional **Gruppierte Fahrzeugansicht mit Links anlegen** aktivieren.
8. Optional **Ladediagramm anlegen** aktivieren.

Die Instanz kann auch ohne gewählte Datenquelle angelegt werden und bleibt dann inaktiv.

## Automatische Zuordnung

Die automatische Zuordnung verwendet ausschließlich den **exakten technischen Objekt-Ident**.

Es werden keine Variablennamen wie `Ladezustand`, `SOC`, `Battery` oder andere frei änderbare Bezeichnungen ausgewertet.

Für jeden Datenpunkt wird geprüft:

1. Existiert unter der gewählten Quellinstanz eine Variable mit exakt dem erwarteten Ident?
2. Entspricht ihr Variablentyp dem erwarteten Typ?
3. Ist der Treffer eindeutig?

Nur dann wird die Variable automatisch verwendet. Bei keinem Treffer, falschem Typ oder Mehrdeutigkeit bleibt der Datenpunkt unzugeordnet und kann manuell ausgewählt werden.

Die Suche umfasst die Quellinstanz und bis zu zwei darunterliegende Ebenen aus Kategorien oder Instanzen.

## Zuordnungsstatus

Direkt unter der Datenquelle zeigt die Instanzkonfiguration die kompakte Übersicht:

- 🟢 **Automatisch erkannt: X**
- 🔵 **Manuell zugeordnet: X**
- 🟠 **Fehlt: X**
- **X von 32 Datenpunkten verfügbar**

Welcher Datenpunkt automatisch erkannt wurde oder noch fehlt, ist im Bereich **Manuelle Variablenzuordnung** an den farbigen Markierungen und der automatisch gefundenen Objekt-ID erkennbar.

## Manuelle Variablenzuordnung

Jeder Datenpunkt besitzt eine optionale manuelle `SelectVariable`-Zuordnung.

Priorität:

```text
manuelle Zuordnung > automatische Ident-Zuordnung
```

Ist eine manuelle Auswahl ungültig, nicht mehr vorhanden oder vom falschen Variablentyp, fällt EV Tile automatisch auf die Ident-Zuordnung zurück.

Eine manuelle Auswahl beeinflusst nur diesen einen Datenpunkt.

## Unterstützte MySkoda-Datenpunkte in Version 1.1

Version 1.1 definiert **32 feste Datenpunkte** als Referenzmodell für MySkoda 1.1.

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
| API | `ApiKeyWarning` | API-Key Warnung | Boolean |
| API | `ApiKeyExpiresAtVar` | API-Key gültig bis | Integer |
| API | `RequestsRemaining` | Verbleibende API-Anfragen | Integer |
| API | `PartialErrors` | API-Teilfehler | String |
| API | `NewApiFeatures` | Neue API-Funktionen | Integer |
| API | `PendingCommands` | Ausstehende Befehle | Integer |
| API | `CommandStatus` | Befehlsstatus | String |

Die beiden Datenpunkte `PendingCommands` und `CommandStatus` werden vom MySkoda-Modul nur angelegt, wenn dort die Detail- und Diagnosevariablen aktiviert sind. Fehlen diese Variablen, bleiben die entsprechenden EV-Tile-Zuordnungen einfach leer.

## Native Objektstruktur

Die Option **Gruppierte Fahrzeugansicht mit Links anlegen** ist standardmäßig **aus**, da EV Tile hierfür zusätzliche Symcon-Objekte anlegt.

Nach ausdrücklicher Aktivierung entsteht unterhalb der EV-Tile-Instanz abhängig von den verfügbaren Daten ungefähr:

```text
EV Tile
├── Fahrzeug
│   ├── Fahrzeugname
│   ├── Kennzeichen
│   ├── Ladezustand
│   ├── Reichweite
│   ├── Kilometerstand
│   ├── Verriegelt
│   ├── Parkstatus
│   ├── Laden
│   ├── Ladeleistung
│   ├── Ladelimit
│   ├── Klimatisierung
│   ├── Solltemperatur
│   └── Letzte Aktualisierung
├── Status
├── Laden
├── Klima
├── Standort
├── API
│   ├── API-Key Warnung
│   ├── API-Key gültig bis
│   ├── Verbleibende API-Anfragen
│   ├── API-Teilfehler
│   ├── Neue API-Funktionen
│   ├── Ausstehende Befehle
│   └── Befehlsstatus
└── Diagramme
```

### Fahrzeug als Übersicht

Die Gruppe **Fahrzeug** ist die kompakte Gesamtübersicht. Sie enthält zusätzlich zu den Fahrzeugstammdaten die wichtigsten Werte aus Laden, Status und Klima.

Damit eignet sie sich als einzelnes Element in der Kachelvisualisierung.

### API-Gruppe

Die Gruppe **API** fasst die technischen MySkoda-Diagnosewerte zusammen. In Version 1.1 gehören dazu zusätzlich die Befehlsdiagnosewerte `PendingCommands` und `CommandStatus`.

Bei einem Update von EV Tile 1.0 auf 1.1 wird die von EV Tile angelegte Standardgruppe **Diagnose** in **API** umbenannt. Wurde die Gruppe vom Benutzer bereits selbst umbenannt, bleibt der Benutzername unverändert.

### Links bleiben original

Alle angezeigten Werte sind Links auf die Originalvariablen. EV Tile:

- legt keine Spiegelvariablen an,
- kopiert keine Werte,
- vergibt den Links **keinen eigenen Anzeigenamen**,
- vergibt den Links **kein eigenes Icon**.

Dadurch übernimmt die Darstellung den Namen, das Profil, das Icon und vorhandene Aktionen der Zielvariable.

Nur die Gruppeninstanzen selbst erhalten bei ihrer Erstanlage einen sinnvollen Namen, ein Icon und eine Position. Bestehende Benutzeranpassungen werden später nicht fortlaufend überschrieben.

Wenn sich eine Zuordnung ändert, darf EV Tile einen selbst angelegten Link auf die neue Zielvariable umstellen, solange der Benutzer das Linkziel nicht zwischenzeitlich selbst verändert hat.

## Kachelvisualisierung

EV Tile hat keinen eigenen HTML-Kacheltyp (`SetVisualizationType(0)`). Die native Objektstruktur wird von Symcon selbst visualisiert.

Für die gewünschte kompakte Ansicht:

1. EV Tile in der Kachelvisualisierung hinzufügen.
2. Darstellung **Einzelnes Element** wählen.
3. Als Element **Fahrzeug** auswählen.

Die Einstellung **Einzelnes Element → Fahrzeug** ist Bestandteil der Symcon-Visualisierungskonfiguration. Sie kann vom PHP-Modul nicht zuverlässig vorbelegt werden und wird deshalb einmalig vom Benutzer in der Visualisierung gewählt.

## Diagramme

Die Option **Ladediagramm anlegen** ist standardmäßig **aus**.

Nach ausdrücklicher Aktivierung legt EV Tile einmalig die Gruppe **Diagramme** und darin das native Symcon-Diagramm **Ladeübersicht** an.

Das Diagramm enthält:

- `StateOfCharge` – Ladezustand auf der linken Achse
- `TargetSOC` – Ladelimit auf der linken Achse
- `ChargePower` – Ladeleistung auf der rechten Achse

Alle drei Datenpunkte müssen zugeordnet sein. Fehlt einer davon, wird kein unvollständiges Diagramm erzeugt und EV Tile schreibt einen Hinweis ins Instanz-Log.

### Archivierung

Ein Symcon-Diagramm benötigt archivierte Quelldaten für einen zeitlichen Verlauf. EV Tile verändert die Archive-Control-Einstellungen **nicht selbstständig**.

Beim MySkoda-Modul kann die Archivierung von Ladezustand, Ladelimit und Ladeleistung ausdrücklich aktiviert werden. Alternativ kann der Benutzer das Logging direkt im Archive Control konfigurieren.

Die Diagrammkonfiguration wird nur bei der Erstanlage gesetzt. Spätere Anpassungen des Benutzers werden nicht bei jedem `ApplyChanges()` überschrieben.

## Objekt- und Referenzverhalten

Die gewählte Quellinstanz und alle effektiv verwendeten Variablen werden mit `RegisterReference()` referenziert.

EV Tile verändert keine Properties der Fahrzeug-Instanz, ruft kein `IPS_ApplyChanges()` auf fremden Instanzen auf und verändert keine Archivierung ohne Benutzeraktion.

## Instanzstatus

| Code | Bedeutung |
|---:|---|
| `102` | Fahrzeugdaten erfolgreich zugeordnet |
| `104` | keine Fahrzeug-Instanz ausgewählt |
| `201` | ausgewählte Fahrzeug-Instanz existiert nicht |
| `202` | keine unterstützten Fahrzeugvariablen gefunden |

## Fehlersuche

- **Automatisch erkannt: 0** – richtige Fahrzeug-Instanz und technische Idents prüfen.
- **Fehlt trotz passendem Ident** – Variablentyp prüfen.
- **Mehrdeutige Zuordnung** – gewünschten Wert manuell zuordnen.
- **Ausstehende Befehle/Befehlsstatus fehlen** – im MySkoda-Modul die Detail- und Diagnosevariablen aktivieren.
- **Objektstruktur fehlt** – Option **Gruppierte Fahrzeugansicht mit Links anlegen** aktivieren und übernehmen.
- **Diagramm fehlt** – `StateOfCharge`, `TargetSOC` und `ChargePower` müssen vorhanden sein.
- **Diagramm ohne Verlauf** – Logging der drei Quellvariablen im Archive Control prüfen.
- **Fahrzeug wird nicht automatisch als Kachelelement gewählt** – in der Kachelvisualisierung einmalig **Einzelnes Element → Fahrzeug** einstellen.

## Datenschutz und externe Dienste

EV Tile kommuniziert mit **keinem externen Dienst**. Alle Daten stammen aus der lokalen Symcon-Installation. Der Netzwerkzugriff auf einen Fahrzeughersteller ist Aufgabe des jeweils verwendeten Fahrzeugmoduls.

## Hilfe und Quellcode

- Repository: `https://github.com/taloriko/IPSymconEVTile`
- Dokumentation: `https://github.com/taloriko/IPSymconEVTile/blob/main/EVTile/README.md`
- Issues: `https://github.com/taloriko/IPSymconEVTile/issues`

## Lizenz

Copyright © 2026 **taloriko**.

Dieses Projekt wird unter der [MIT-Lizenz](../LICENSE) veröffentlicht.
