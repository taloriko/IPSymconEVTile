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


def variable_type(mapping: str, ident: str) -> str:
    match = re.search(
        rf"'ident'\s*=>\s*'{re.escape(ident)}'.*?'types'\s*=>\s*\[VARIABLETYPE_(BOOLEAN|INTEGER|FLOAT|STRING)\]",
        mapping,
        flags=re.S,
    )
    assert match is not None, ident
    return match.group(1)


def main() -> None:
    library = load(ROOT / "library.json")
    module = load(ROOT / "EVTile" / "module.json")
    form = load(ROOT / "EVTile" / "form.json")
    locale = load(ROOT / "EVTile" / "locale.json")
    translations = locale.get("translations", {}).get("de", {})

    assert library["id"] == "{DD739BDB-F2DD-4DF6-899C-A6078E7D077B}"
    assert GUID.match(library["id"])
    assert library["version"] == "1.1"
    assert library["compatibility"]["version"] >= "8.1"

    assert module["id"] == "{07A7C7B9-9540-4636-ABB5-2BA4A1336B13}"
    assert GUID.match(module["id"])
    assert module["name"] == "EV Tile"
    assert module["type"] == 3
    assert module["prefix"] == "EVTILE"

    for required in [
        ROOT / "README.md",
        ROOT / "CHANGELOG.md",
        ROOT / "LICENSE",
        ROOT / "EVTile" / "README.md",
        ROOT / "EVTile" / "module.php",
        ROOT / "EVTile" / "module.json",
        ROOT / "EVTile" / "form.json",
        ROOT / "EVTile" / "locale.json",
        ROOT / "EVTile" / "src" / "CoreTrait.php",
        ROOT / "EVTile" / "src" / "MappingTrait.php",
        ROOT / "EVTile" / "src" / "ObjectTreeTrait.php",
        ROOT / "EVTile" / "src" / "ChartTrait.php",
    ]:
        assert required.is_file(), required

    all_captions = {
        item["caption"]
        for section in ("elements", "actions", "status")
        for item in walk(form.get(section, []))
        if isinstance(item.get("caption"), str)
    }
    missing = sorted(caption for caption in all_captions if caption not in translations)
    assert not missing, f"Missing German form translations: {missing}"

    module_php = (ROOT / "EVTile" / "module.php").read_text(encoding="utf-8")
    core = (ROOT / "EVTile" / "src" / "CoreTrait.php").read_text(encoding="utf-8")
    mapping = (ROOT / "EVTile" / "src" / "MappingTrait.php").read_text(encoding="utf-8")
    tree = (ROOT / "EVTile" / "src" / "ObjectTreeTrait.php").read_text(encoding="utf-8")
    chart = (ROOT / "EVTile" / "src" / "ChartTrait.php").read_text(encoding="utf-8")
    php_sources = "\n".join(
        path.read_text(encoding="utf-8") for path in (ROOT / "EVTile").rglob("*.php")
    )

    assert "final class EVTile extends IPSModuleStrict" in module_php
    assert "SetVisualizationType(0)" in core
    assert "RegisterTimer" not in php_sources
    assert "RegisterVariable" not in php_sources
    assert "SetValue(" not in php_sources

    group_section = mapping.split("private function groupDefinitions", 1)[1].split(
        "private function roleDefinitions", 1
    )[0]
    group_order = re.findall(r"'([^']+)'\s*=>\s*\['label'", group_section)
    assert group_order == [
        "overview",
        "vehicle",
        "airConditioning",
        "charging",
        "odometer",
        "parkingPosition",
        "statusOverall",
        "statusDetail",
        "module",
        "vin",
        "apiVehicle",
        "charts",
    ]

    expected_idents = [
        "VehicleName",
        "LicensePlate",
        "VIN",
        "ClimateState",
        "AirConditioningAtUnlock",
        "TargetTemperature",
        "TargetTemperatureUnit",
        "WindowHeatingEnabled",
        "WindowHeatingFront",
        "WindowHeatingRear",
        "AtSavedChargingLocation",
        "AutoUnlockPlug",
        "BatteryCareTargetSOC",
        "BatteryCareMode",
        "MaxChargeCurrentAC",
        "ChargeMode",
        "TargetSOC",
        "Range",
        "StateOfCharge",
        "ChargePower",
        "FullyChargedAt",
        "RemainingChargingTime",
        "ChargingState",
        "ChargeType",
        "Mileage",
        "ParkingState",
        "ParkingAddress",
        "Latitude",
        "Longitude",
        "DoorsLocked",
        "Locked",
        "DoorsOpen",
        "WindowsOpen",
        "LightsOn",
        "ReliableLockStatus",
        "SunroofOpen",
        "TrunkOpen",
        "BonnetOpen",
        "Climate",
        "Charging",
        "LastUpdate",
        "ApiKeyWarning",
        "ApiKeyExpiresAtVar",
        "RequestsRemaining",
        "PartialErrors",
        "NewApiFeatures",
        "PendingCommands",
        "CommandStatus",
        "VINWMI",
        "VINVDS",
        "VINVIS",
        "VINManufacturer",
        "VINCountry",
        "VINModel",
        "VINModelCode",
        "VINBody",
        "VINSteering",
        "VINDrive",
        "VINPower",
        "VINVariant",
        "VINRestraint",
        "VINModelYear",
        "VINPlant",
        "VINSerialNumber",
        "VINCheckDigit",
        "APICarType",
        "APIPrimaryEngineType",
        "APISecondaryEngineType",
        "APISupportedFeatures",
        "APIAvailableChargeModes",
        "APIRemoteOperations",
        "APIAuxiliaryHeatingState",
        "APIActiveVentilationState",
    ]
    mapped_idents = re.findall(r"'ident'\s*=>\s*'([^']+)'", mapping)
    assert mapped_idents == expected_idents
    assert len(mapped_idents) == 73
    assert len(set(mapped_idents)) == 73

    for ident in [
        "DoorsLocked",
        "Locked",
        "DoorsOpen",
        "WindowsOpen",
        "LightsOn",
        "ReliableLockStatus",
        "SunroofOpen",
        "TrunkOpen",
        "BonnetOpen",
    ]:
        assert variable_type(mapping, ident) == "STRING"

    assert variable_type(mapping, "Range") == "INTEGER"
    assert variable_type(mapping, "Mileage") == "INTEGER"
    assert variable_type(mapping, "ChargePower") == "FLOAT"
    assert variable_type(mapping, "TargetTemperature") == "FLOAT"

    overview_match = re.search(
        r"private function overviewRoles\(\): array\s*\{\s*return \[(.*?)\];",
        tree,
        flags=re.S,
    )
    assert overview_match is not None
    overview_roles = re.findall(r"'([^']+)'", overview_match.group(1))
    assert overview_roles == [
        "soc",
        "range",
        "charging",
        "chargingState",
        "chargePower",
        "targetSoc",
        "remainingChargingTime",
        "climate",
        "climateState",
        "targetTemperature",
        "mileage",
        "reliableLockStatus",
        "doorsOpen",
        "windowsOpen",
        "parkingState",
        "lastUpdate",
        "vehicleName",
        "licensePlate",
    ]

    assert "$groupKey === 'overview'" in tree
    assert "vehicleOverviewRoles" not in tree
    assert "diagnostics" not in mapping
    assert "Diagnose" not in tree
    assert "Diagnostics" not in tree

    # Current managed order is applied to existing EV Tile objects as well.
    assert "IPS_SetPosition($existingId, (int) $group['position'])" in tree
    assert "IPS_SetPosition($existingId, $position)" in tree

    assert "IPS_CreateInstance" in tree
    assert "IPS_CreateLink" in tree
    assert "IPS_SetLinkTargetID" in tree
    assert "IPS_SetName($linkId" not in tree
    assert "IPS_SetIcon($linkId" not in tree

    assert "MEDIATYPE_CHART" in chart
    assert "'soc'" in chart
    assert "'targetSoc'" in chart
    assert "'chargePower'" in chart

    for forbidden in [
        "SetVisualizationType(1)",
        "GetVisualizationTile",
        "UpdateVisualizationValue",
        "MessageSink",
        "VM_UPDATE",
        "IPS_SetProperty",
        "IPS_ApplyChanges",
        "AC_SetLoggingStatus",
    ]:
        assert forbidden not in php_sources

    root_readme = (ROOT / "README.md").read_text(encoding="utf-8")
    module_readme = (ROOT / "EVTile" / "README.md").read_text(encoding="utf-8")
    changelog = (ROOT / "CHANGELOG.md").read_text(encoding="utf-8")

    for text in [
        "MySkoda 1.5",
        "73 unterstützte Variablen",
        "Übersicht",
        "Moduldaten",
        "FIN-Daten",
        "API-Fahrzeugdaten",
        "keine eigene HTML-Visualisierung",
    ]:
        assert text in root_readme

    for text in [
        "MySkoda 1.5",
        "73 Variablen",
        "API-Reihenfolge",
        "kein Boolean-Fallback",
        "RegisterReference()",
        "Übersicht",
    ]:
        assert text in module_readme

    assert "MySkoda 1.1" not in root_readme
    assert "32 definierte MySkoda-Datenpunkte" not in root_readme
    assert "MySkoda 1.5" in changelog
    assert "keine Abwärtskompatibilität" in changelog


if __name__ == "__main__":
    main()
