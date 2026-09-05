import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
GUID = re.compile(r"^\{[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}\}$")

with (ROOT / 'library.json').open(encoding='utf-8') as f:
    library = json.load(f)
with (ROOT / 'EVTile' / 'module.json').open(encoding='utf-8') as f:
    module = json.load(f)
with (ROOT / 'EVTile' / 'form.json').open(encoding='utf-8') as f:
    form = json.load(f)
with (ROOT / 'EVTile' / 'locale.json').open(encoding='utf-8') as f:
    locale = json.load(f)

assert GUID.match(library['id'])
assert GUID.match(module['id'])
assert library['compatibility']['version'] >= '8.1'
assert library['version'] == '1.3'
assert module['type'] == 3
assert module['vendor'] == 'taloriko'
assert module['prefix'].isalnum()
assert form['elements']
assert 'de' in locale['translations']

php = (ROOT / 'EVTile' / 'module.php').read_text(encoding='utf-8')
html = (ROOT / 'EVTile' / 'module.html').read_text(encoding='utf-8')
assert 'extends IPSModuleStrict' in php
assert 'SetVisualizationType(1)' in php
assert 'GetVisualizationTile' in php
assert 'UpdateVisualizationValue' in php
assert 'RegisterMessage($id, VM_UPDATE)' in php
assert 'RequestAction($id, $value)' in php
assert 'GetConfigurationForm' in php
assert 'assignmentStatus' in php
assert 'resolveAutomaticVariables' in php
assert 'ROLE_NAME_ALIASES' in php
assert "'autoId' => $autoId" in php
assert "(Auto #" in php
assert "$counts['auto']" in php
assert "$counts['manual']" in php
assert "$counts['missing']" in php
assert 'buildState()' in php
assert '$ids = $this->resolveVariables();' in php
assert 'viewBox="0 0 380 220"' in html
assert 'chargeAction' in html
assert 'climateAction' in html
assert 'id="socSegments"' in html
assert 'repeat(20' in html
assert 'function updateSoc' in html
assert 'Math.ceil(s/5)' in html
assert 'const previous=evState || {}' in html
assert 'controls:{...(previous.controls || {}), ...(data.controls || {})}' in html
assert 'battery-shell' not in html
assert 'battery-bars' not in html
assert 'class="battery"' not in html
assert 'ApiKey' not in html
assert 'RequestsRemaining' not in html
assert 'PartialErrors' not in html
