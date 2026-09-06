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

    # Stable repository and module identity.
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
        ROOT / "EVTile" / "module.html",
        ROOT / "EVTile" / "module.json",
        ROOT / "EVTile" / "form.json",
        ROOT / "EVTile" / "locale.json",
    ]:
        assert required.is_file(), required

    # Configuration form and localization.
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
    assert translations.get("Data source") == "Datenquelle"
    assert translations.get("Assignment status") == "Zuordnungsstatus"
    assert translations.get("Manual variable assignment") == "Manuelle Variablenzuordnung"
    assert translations.get("Help and documentation") == "Hilfe und Dokumentation"

    module_php = (ROOT / "EVTile" / "module.php").read_text(encoding="utf-8")
    core = (ROOT / "EVTile" / "src" / "CoreTrait.php").read_text(encoding="utf-8")
    mapping = (ROOT / "EVTile" / "src" / "MappingTrait.php").read_text(encoding="utf-8")
    tree = (ROOT / "EVTile" / "src" / "ObjectTreeTrait.php").read_text(encoding="utf-8")
    visualization = (ROOT / "EVTile" / "src" / "VisualizationTrait.php").read_text(encoding="utf-8")
    html = (ROOT / "EVTile" / "module.html").read_text(encoding="utf-8")
    php_sources = "\n".join(
        path.read_text(encoding="utf-8") for path in (ROOT / "EVTile").rglob("*.php")
    )

    # Clean, strict module architecture.
    assert "final class EVTile extends IPSModuleStrict" in module_php
    for trait in ["CoreTrait.php", "MappingTrait.php", "ObjectTreeTrait.php", "VisualizationTrait.php"]:
        assert trait in module_php
    expected_sources = {"CoreTrait.php", "MappingTrait.php", "ObjectTreeTrait.php", "VisualizationTrait.php"}
    assert {p.name for p in (ROOT / "EVTile" / "src").glob("*.php")} == expected_sources
    assert re.search(r"<\?(?!php)", php_sources) is None
    assert "IPS_LogMessage" not in php_sources

    # Automatic mapping is exact Ident based, with type validation and no name aliases.
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

    # Source and resolved variables are referenced and watched without polling.
    assert "RegisterPropertyInteger('SourceInstanceID', 0)" in core
    assert "RegisterReference($sourceId)" in core
    assert "RegisterReference($id)" in core
    assert "RegisterMessage($id, VM_UPDATE)" in core
    assert "RegisterTimer" not in php_sources

    # Object creation requires explicit opt-in; values are linked, never mirrored.
    assert "RegisterPropertyBoolean('CreateListView', false)" in core
    assert "ReadPropertyBoolean('CreateListView')" in core
    assert "ReadPropertyBoolean('CreateListView')" in tree
    assert "IPS_CreateInstance" in tree
    assert "IPS_CreateLink" in tree
    assert "IPS_SetLinkTargetID" in tree
    assert "ManagedLinkTargets" in core
    assert "previousManagedTarget" in tree
    assert "RegisterVariable" not in tree
    assert "SetValue" not in tree
    assert "IPS_Delete" not in tree
    assert "UnregisterVariable" not in php_sources

    # Existing generated object names/icons/positions are only set in creation branches.
    assert "ensureGroup" in tree and "ensureLink" in tree
    assert "IPS_SetName" in tree
    assert "IPS_SetIcon" in tree
    assert "IPS_SetPosition" in tree

    # Display-only HTML SDK implementation.
    assert "SetVisualizationType(1)" in core
    assert "GetVisualizationTile(): string" in visualization
    assert "MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void" in visualization
    assert "UpdateVisualizationValue" in visualization
    assert "VM_UPDATE" in visualization
    assert "function handleMessage(data)" in html
    assert "requestAction(" not in html
    assert "RequestAction(" not in php_sources
    assert "repeat(20" in html
    assert "prefers-color-scheme: dark" in html

    # Do not manipulate source properties, archive configuration or custom presentations/actions.
    for forbidden in [
        "IPS_SetProperty", "IPS_ApplyChanges", "AC_SetLoggingStatus", "AC_SetAggregationType",
        "IPS_SetVariableCustomProfile", "IPS_SetVariableCustomAction", "IPS_CreateVariableProfile",
        "IPS_CreateTemplate", "IPS_SetTemplate"
    ]:
        assert forbidden not in php_sources

    # Documentation is explicitly initial 1.0 and explains the open architecture.
    root_readme = (ROOT / "README.md").read_text(encoding="utf-8")
    module_readme = (ROOT / "EVTile" / "README.md").read_text(encoding="utf-8")
    changelog = (ROOT / "CHANGELOG.md").read_text(encoding="utf-8")

    for text in [
        "herstellerunabhängiges Visualisierungsmodul",
        "MySkoda",
        "exakten technischen Ident",
        "automatisch erkannt / manuell zugeordnet / fehlt",
        "standardmäßig deaktiviert",
        "Anzeige-only",
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
        "Gruppierte Listenansicht",
        "Kachelvisualisierung",
        "Anzeige-only",
        "Datenschutz und externe Dienste",
        "RegisterReference()",
        "VM_UPDATE"
    ]:
        assert text in module_readme

    assert "## 1.0 - 2026-09-06" in changelog
    for old_version in ["## 1.1", "## 1.2", "## 1.3", "## 2."]:
        assert old_version not in changelog


if __name__ == "__main__":
    main()
