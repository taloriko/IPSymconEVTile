from __future__ import annotations

import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
GUID = re.compile(r"^\{[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}\}$")


def load(path: Path):
    with path.open("r", encoding="utf-8") as handle:
        return json.load(handle)


def walk(items):
    for item in items:
        if not isinstance(item, dict):
            continue
        yield item
        nested = item.get("items")
        if isinstance(nested, list):
            yield from walk(nested)


def captions(items):
    return {
        item["caption"]
        for item in walk(items)
        if isinstance(item.get("caption"), str)
    }


def main() -> None:
    library = load(ROOT / "library.json")
    module = load(ROOT / "EVTile" / "module.json")
    form = load(ROOT / "EVTile" / "form.json")
    locale = load(ROOT / "EVTile" / "locale.json")
    translations = locale.get("translations", {}).get("de", {})

    assert set(library) == {
        "id", "author", "name", "url", "compatibility", "version", "build", "date"
    }
    assert library["id"] == "{DD739BDB-F2DD-4DF6-899C-A6078E7D077B}"
    assert GUID.match(library["id"])
    assert library["author"] == "taloriko"
    assert library["name"] == "EV Tile"
    assert library["url"] == "https://github.com/taloriko/IPSymconEVTile"
    assert library["compatibility"]["version"] >= "8.1"
    assert library["version"] == "1.0"
    assert library["build"] == 0
    assert isinstance(library["date"], int) and library["date"] > 0

    assert set(module) == {
        "id", "name", "type", "vendor", "aliases", "parentRequirements",
        "childRequirements", "implemented", "prefix", "url"
    }
    assert module["id"] == "{07A7C7B9-9540-4636-ABB5-2BA4A1336B13}"
    assert GUID.match(module["id"])
    assert module["name"] == "EV Tile"
    assert module["type"] == 3
    assert module["vendor"] == "taloriko"
    assert module["prefix"] == "EVTILE"
    assert module["url"] == "https://github.com/taloriko/IPSymconEVTile/blob/main/EVTile/README.md"

    for required in [
        ROOT / "README.md",
        ROOT / "CHANGELOG.md",
        ROOT / "LICENSE",
        ROOT / ".gitignore",
        ROOT / "EVTile" / "README.md",
        ROOT / "EVTile" / "module.php",
        ROOT / "EVTile" / "module.json",
        ROOT / "EVTile" / "form.json",
        ROOT / "EVTile" / "locale.json",
    ]:
        assert required.is_file(), required

    assert not (ROOT / "EVTile" / "module.html").exists()
    assert not (ROOT / "EVTile" / "src" / "VisualizationTrait.php").exists()

    assert isinstance(translations, dict) and translations
    all_captions = set()
    all_captions |= captions(form.get("elements", []))
    all_captions |= captions(form.get("actions", []))
    all_captions |= captions(form.get("status", []))
    missing = sorted(c for c in all_captions if c not in translations)
    assert not missing, f"Missing German form translations: {missing}"

    elements = list(walk(form.get("elements", [])))
    source = [e for e in elements if e.get("name") == "SourceInstanceID"]
    assert len(source) == 1 and source[0].get("type") == "SelectInstance"
    list_view = [e for e in elements if e.get("name") == "CreateListView"]
    assert len(list_view) == 1 and list_view[0].get("type") == "CheckBox"
    chart_option = [e for e in elements if e.get("name") == "CreateChargingChart"]
    assert len(chart_option) == 1 and chart_option[0].get("type") == "CheckBox"

    for source_text, german in {
        "Data source": "Datenquelle",
        "Object structure": "Objektstruktur",
        "Charts": "Diagramme",
        "Assignment status": "Zuordnungsstatus",
        "Manual variable assignment": "Manuelle Variablenzuordnung",
        "Help and documentation": "Hilfe und Dokumentation",
        "Charging overview": "Ladeübersicht",
    }.items():
        assert translations.get(source_text) == german

    module_php = (ROOT / "EVTile" / "module.php").read_text(encoding="utf-8")
    core = (ROOT / "EVTile" / "src" / "CoreTrait.php").read_text(encoding="utf-8")
    mapping = (ROOT / "EVTile" / "src" / "MappingTrait.php").read_text(encoding="utf-8")
    tree = (ROOT / "EVTile" / "src" / "ObjectTreeTrait.php").read_text(encoding="utf-8")
    chart = (ROOT / "EVTile" / "src" / "ChartTrait.php").read_text(encoding="utf-8")
    php_sources = "\n".join(
        path.read_text(encoding="utf-8") for path in (ROOT / "EVTile").rglob("*.php")
    )

    assert "final class EVTile extends IPSModuleStrict" in module_php
    for trait in ["CoreTrait.php", "MappingTrait.php", "ObjectTreeTrait.php", "ChartTrait.php"]:
        assert trait in module_php
    expected_sources = {"CoreTrait.php", "MappingTrait.php", "ObjectTreeTrait.php", "ChartTrait.php"}
    assert {p.name for p in (ROOT / "EVTile" / "src").glob("*.php")} == expected_sources
    assert re.search(r"<\?(?!php)", php_sources) is None
    assert "IPS_LogMessage" not in php_sources
    assert "SetVisualizationType(0)" in core
    for forbidden in [
        "SetVisualizationType(1)", "GetVisualizationTile", "UpdateVisualizationValue",
        "MessageSink", "VM_UPDATE", "handleMessage"
    ]:
        assert forbidden not in php_sources

    assert "resolveAutomaticCandidates" in mapping
    assert "ObjectIdent" in mapping
    assert "$catalog[$ident]" in mapping
    assert "VariableType" in mapping
    assert "isValidVariableForRole" in mapping
    assert "ROLE_NAME_ALIASES" not in php_sources
    assert "ROLE_ALIASES" not in php_sources
    assert "normalizeLookupKey" not in php_sources

    required_idents = [
        "VehicleName", "LicensePlate", "StateOfCharge", "Range", "Mileage",
        "Locked", "DoorsOpen", "WindowsOpen", "TrunkOpen", "BonnetOpen",
        "SunroofOpen", "LightsOn", "ParkingState", "Charging", "ChargingState",
        "ChargeType", "ChargePower", "TargetSOC", "ChargeMode", "FullyChargedAt",
        "Climate", "TargetTemperature", "Latitude", "Longitude", "ApiKeyWarning",
        "ApiKeyExpiresAtVar", "RequestsRemaining", "PartialErrors", "NewApiFeatures",
        "LastUpdate"
    ]
    assert len(required_idents) == 30
    for ident in required_idents:
        assert f"'ident' => '{ident}'" in mapping

    assert "RegisterPropertyInteger('SourceInstanceID', 0)" in core
    assert "RegisterReference($sourceId)" in core
    assert "RegisterReference($id)" in core
    assert "RegisterTimer" not in php_sources
    assert "RegisterVariable" not in php_sources
    assert "SetValue(" not in php_sources

    # Compact status: counts only, no per-role/group detail loop.
    assert "insertAssignmentStatusPanel" in core
    assert "Automatically detected" in core
    assert "Manually assigned" in core
    assert "Missing" in core
    assert "%d of %d data points available" in core
    status_method = core.split("private function insertAssignmentStatusPanel", 1)[1].split(
        "private function insertManualAssignmentPanel", 1
    )[0]
    assert "groupDefinitions" not in status_method
    assert "foreach ($this->roleDefinitions()" not in status_method

    assert "RegisterPropertyBoolean('CreateListView', false)" in core
    assert "ReadPropertyBoolean('CreateListView')" in core
    assert "ReadPropertyBoolean('CreateListView')" in tree
    assert "IPS_CreateInstance" in tree
    assert "IPS_CreateLink" in tree
    assert "IPS_SetLinkTargetID" in tree
    assert "ManagedLinkTargets" in core
    assert "previousManagedTarget" in tree
    assert "IPS_Delete" not in tree

    assert "vehicleOverviewRoles" in tree
    for overview_role in [
        "vehicleName", "licensePlate", "soc", "range", "mileage", "locked",
        "charging", "chargePower", "targetSoc", "climate", "targetTemperature", "lastUpdate"
    ]:
        assert f"'{overview_role}'" in tree

    # Links receive no custom name/icon. Symcon mirrors the target presentation.
    assert "IPS_SetName($linkId" not in tree
    assert "IPS_SetIcon($linkId" not in tree
    assert "IPS_SetName($id, $this->Translate" in tree
    assert "IPS_SetIcon($id" in tree
    assert "IPS_SetPosition($id" in tree

    assert "RegisterPropertyBoolean('CreateChargingChart', false)" in core
    assert "ReadPropertyBoolean('CreateChargingChart')" in core
    assert "ReadPropertyBoolean('CreateChargingChart')" in chart
    assert "MEDIATYPE_CHART" in chart
    assert "IPS_CreateMedia(MEDIATYPE_CHART)" in chart
    assert "EVTILE_Chart_Charging" in chart
    assert "'soc'" in chart and "'targetSoc'" in chart and "'chargePower'" in chart
    assert "'axis' => 0" in chart
    assert "'axis' => 1" in chart
    assert "'side' => 'left'" in chart
    assert "'side' => 'right'" in chart
    assert "'~Battery.100'" in chart
    assert "'~Power'" in chart
    assert "IPS_SetMediaContent" in chart
    assert "IPS_SendMediaEvent" in chart

    for forbidden in [
        "IPS_SetProperty", "IPS_ApplyChanges", "AC_SetLoggingStatus", "AC_SetAggregationType",
        "AC_SetGraphStatus", "IPS_SetVariableCustomProfile", "IPS_SetVariableCustomAction",
        "IPS_CreateVariableProfile", "IPS_CreateTemplate", "IPS_SetTemplate"
    ]:
        assert forbidden not in php_sources

    root_readme = (ROOT / "README.md").read_text(encoding="utf-8")
    module_readme = (ROOT / "EVTile" / "README.md").read_text(encoding="utf-8")
    changelog = (ROOT / "CHANGELOG.md").read_text(encoding="utf-8")

    for text in [
        "herstellerunabhängiges IP-Symcon-Modul",
        "MySkoda",
        "exakten technischen Ident",
        "automatisch erkannt / manuell zugeordnet / fehlt",
        "Originalvariablen",
        "keine eigene HTML-Visualisierung",
        "Einzelnes Element",
        "Fahrzeug",
        "Ladediagramm",
        "Archive Control",
        "keine externen Netzwerkaufrufe",
        "IP-Symcon **8.1 oder neuer**",
        "MIT-Lizenz"
    ]:
        assert text in root_readme

    for text in [
        "Automatische Zuordnung",
        "Manuelle Variablenzuordnung",
        "Zuordnungsstatus",
        "30",
        "Native Objektstruktur",
        "keine eigene HTML-Visualisierung",
        "Einzelnes Element",
        "Diagramme",
        "Archive Control",
        "Datenschutz und externe Dienste",
        "RegisterReference()"
    ]:
        assert text in module_readme

    assert "## 1.0 - 2026-09-06" in changelog
    assert "no HTML visualization" in changelog
    for old_version in ["## 1.1", "## 1.2", "## 1.3", "## 2."]:
        assert old_version not in changelog


if __name__ == "__main__":
    main()
