<?php

declare(strict_types=1);

final class EVTile extends IPSModuleStrict
{
    private const ROLE_PROPERTIES = [
        'soc' => 'StateOfChargeID',
        'range' => 'RangeID',
        'mileage' => 'MileageID',
        'locked' => 'LockedID',
        'doorsOpen' => 'DoorsOpenID',
        'windowsOpen' => 'WindowsOpenID',
        'trunkOpen' => 'TrunkOpenID',
        'bonnetOpen' => 'BonnetOpenID',
        'sunroofOpen' => 'SunroofOpenID',
        'lightsOn' => 'LightsOnID',
        'charging' => 'ChargingID',
        'chargePower' => 'ChargePowerID',
        'targetSoc' => 'TargetSOCID',
        'chargingState' => 'ChargingStateID',
        'chargeType' => 'ChargeTypeID',
        'chargeMode' => 'ChargeModeID',
        'fullyChargedAt' => 'FullyChargedAtID',
        'climate' => 'ClimateID',
        'targetTemperature' => 'TargetTemperatureID',
        'vehicleName' => 'VehicleNameID',
        'licensePlate' => 'LicensePlateID',
        'parkingState' => 'ParkingStateID',
        'lastUpdate' => 'LastUpdateID'
    ];

    private const ROLE_LABELS = [
        'soc' => 'State of charge',
        'range' => 'Range',
        'mileage' => 'Mileage',
        'locked' => 'Locked',
        'doorsOpen' => 'Doors open',
        'windowsOpen' => 'Windows open',
        'trunkOpen' => 'Trunk open',
        'bonnetOpen' => 'Bonnet open',
        'sunroofOpen' => 'Sunroof open',
        'lightsOn' => 'Lights on',
        'charging' => 'Charging',
        'chargePower' => 'Charging power',
        'targetSoc' => 'Charging limit',
        'chargingState' => 'Charging state',
        'chargeType' => 'Charge type',
        'chargeMode' => 'Charging mode',
        'fullyChargedAt' => 'Fully charged at',
        'climate' => 'Air conditioning',
        'targetTemperature' => 'Target temperature',
        'vehicleName' => 'Vehicle name',
        'licensePlate' => 'License plate',
        'parkingState' => 'Parking state',
        'lastUpdate' => 'Last update'
    ];

    private const ROLE_ALIASES = [
        'soc' => ['StateOfCharge', 'SOC', 'BatterySOC', 'BatteryLevel'],
        'range' => ['Range', 'RemainingRange', 'ElectricRange'],
        'mileage' => ['Mileage', 'Odometer'],
        'locked' => ['Locked', 'VehicleLocked'],
        'doorsOpen' => ['DoorsOpen'],
        'windowsOpen' => ['WindowsOpen'],
        'trunkOpen' => ['TrunkOpen', 'BootOpen'],
        'bonnetOpen' => ['BonnetOpen', 'HoodOpen'],
        'sunroofOpen' => ['SunroofOpen', 'RoofOpen'],
        'lightsOn' => ['LightsOn'],
        'charging' => ['Charging', 'IsCharging'],
        'chargePower' => ['ChargePower', 'ChargingPower'],
        'targetSoc' => ['TargetSOC', 'ChargeLimit', 'ChargingLimit'],
        'chargingState' => ['ChargingState', 'ChargeState'],
        'chargeType' => ['ChargeType', 'ChargingType'],
        'chargeMode' => ['ChargeMode', 'ChargingMode'],
        'fullyChargedAt' => ['FullyChargedAt', 'ChargeEndTime'],
        'climate' => ['Climate', 'AirConditioning'],
        'targetTemperature' => ['TargetTemperature', 'ClimateTargetTemperature'],
        'vehicleName' => ['VehicleName', 'Name'],
        'licensePlate' => ['LicensePlate', 'RegistrationPlate'],
        'parkingState' => ['ParkingState'],
        'lastUpdate' => ['LastUpdate', 'UpdatedAt']
    ];

    private const ROLE_NAME_ALIASES = [
        'soc' => ['Ladezustand', 'State of charge', 'SOC'],
        'range' => ['Reichweite', 'Range'],
        'mileage' => ['Kilometerstand', 'Mileage', 'Odometer'],
        'locked' => ['Verriegelt', 'Locked'],
        'doorsOpen' => ['Türen offen', 'Tueren offen', 'Doors open'],
        'windowsOpen' => ['Fenster offen', 'Windows open'],
        'trunkOpen' => ['Kofferraum offen', 'Trunk open', 'Boot open'],
        'bonnetOpen' => ['Motorhaube offen', 'Bonnet open', 'Hood open'],
        'sunroofOpen' => ['Schiebedach offen', 'Sunroof open', 'Roof open'],
        'lightsOn' => ['Licht an', 'Lights on'],
        'charging' => ['Laden', 'Charging'],
        'chargePower' => ['Ladeleistung', 'Charging power'],
        'targetSoc' => ['Ladelimit', 'Charging limit', 'Charge limit'],
        'chargingState' => ['Ladestatus', 'Charging state'],
        'chargeType' => ['Ladeart', 'Charge type'],
        'chargeMode' => ['Lademodus', 'Charging mode'],
        'fullyChargedAt' => ['Voraussichtlich voll', 'Fully charged at'],
        'climate' => ['Klimatisierung', 'Air conditioning', 'Climate'],
        'targetTemperature' => ['Klima Solltemperatur', 'Solltemperatur', 'Target temperature'],
        'vehicleName' => ['Fahrzeugname', 'Vehicle name'],
        'licensePlate' => ['Kennzeichen', 'License plate'],
        'parkingState' => ['Parkstatus', 'Parking state'],
        'lastUpdate' => ['Letzte Aktualisierung', 'Last update']
    ];

    public function Create(): void
    {
        parent::Create();

        $this->RegisterPropertyInteger('SourceInstanceID', 0);
        $this->RegisterPropertyBoolean('EnableControls', true);
        foreach (self::ROLE_PROPERTIES as $property) {
            $this->RegisterPropertyInteger($property, 0);
        }

        $this->RegisterAttributeString('ResolvedVariables', '{}');
        $this->SetVisualizationType(1);
    }

    public function ApplyChanges(): void
    {
        parent::ApplyChanges();
        $this->SetVisualizationType(1);

        foreach (array_unique(array_values($this->readResolvedVariables())) as $id) {
            if ($id > 0) {
                $this->UnregisterMessage($id, VM_UPDATE);
            }
        }
        foreach ($this->GetReferenceList() as $referenceId) {
            $this->UnregisterReference($referenceId);
        }

        $source = $this->ReadPropertyInteger('SourceInstanceID');
        if ($source > 0 && IPS_ObjectExists($source)) {
            $this->RegisterReference($source);
        }

        $resolved = $this->resolveVariables();
        foreach (array_unique(array_values($resolved)) as $id) {
            if ($id <= 0 || !IPS_VariableExists($id)) {
                continue;
            }
            $this->RegisterReference($id);
            $this->RegisterMessage($id, VM_UPDATE);
        }

        $this->WriteAttributeString('ResolvedVariables', json_encode($resolved, JSON_UNESCAPED_SLASHES));
        $this->SetStatus($this->hasUsefulData($resolved) ? 102 : 104);

        if (IPS_GetKernelRunlevel() === KR_READY) {
            $this->pushState();
        }
    }

    public function GetConfigurationForm(): string
    {
        $json = @file_get_contents(__DIR__ . '/form.json');
        if ($json === false) {
            return '{}';
        }

        $form = json_decode($json, true);
        if (!is_array($form)) {
            return '{}';
        }

        $automatic = $this->resolveAutomaticVariables();
        $resolved = $this->resolveVariables();
        $status = $this->assignmentStatus($resolved, $automatic);
        $this->patchAssignmentCaptions($form, $status);
        $this->insertAssignmentStatusPanel($form, $status);

        return json_encode($form, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data): void
    {
        if ($Message !== VM_UPDATE) {
            return;
        }
        if (in_array((int) $SenderID, array_values($this->readResolvedVariables()), true)) {
            $this->pushState();
        }
    }

    public function GetVisualizationTile(): string
    {
        $html = (string) @file_get_contents(__DIR__ . '/module.html');
        $state = json_encode($this->buildState(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $html . '<script>if(typeof handleMessage==="function"){handleMessage(' . $state . ');}</script>';
    }

    public function RequestAction($Ident, $Value): void
    {
        if (!$this->ReadPropertyBoolean('EnableControls')) {
            return;
        }

        switch ((string) $Ident) {
            case 'ToggleCharging':
                $this->requestRoleAction('charging', (bool) $Value);
                break;
            case 'ToggleClimate':
                $this->requestRoleAction('climate', (bool) $Value);
                break;
            case 'SetTargetSOC':
                $this->requestRoleAction('targetSoc', (int) $Value);
                break;
            case 'SetTargetTemperature':
                $this->requestRoleAction('targetTemperature', (float) $Value);
                break;
            default:
                throw new InvalidArgumentException('Unknown action: ' . (string) $Ident);
        }
    }

    private function resolveVariables(): array
    {
        $resolved = $this->resolveAutomaticVariables();
        foreach (self::ROLE_PROPERTIES as $role => $property) {
            $manual = $this->ReadPropertyInteger($property);
            if ($manual > 0 && IPS_VariableExists($manual)) {
                $resolved[$role] = $manual;
            }
        }
        return $resolved;
    }

    private function resolveAutomaticVariables(): array
    {
        $catalog = $this->collectVariableCatalog($this->ReadPropertyInteger('SourceInstanceID'));
        $resolved = array_fill_keys(array_keys(self::ROLE_PROPERTIES), 0);

        foreach (self::ROLE_PROPERTIES as $role => $_property) {
            foreach (self::ROLE_ALIASES[$role] ?? [] as $alias) {
                $key = strtolower($alias);
                if (isset($catalog['ident'][$key])) {
                    $resolved[$role] = $catalog['ident'][$key];
                    break;
                }
            }

            if ($resolved[$role] > 0) {
                continue;
            }

            foreach (self::ROLE_NAME_ALIASES[$role] ?? [] as $alias) {
                $key = $this->normalizeLookupKey($alias);
                if ($key !== '' && isset($catalog['name'][$key])) {
                    $resolved[$role] = $catalog['name'][$key];
                    break;
                }
            }
        }

        return $resolved;
    }

    private function collectVariableCatalog(int $root): array
    {
        $result = ['ident' => [], 'name' => []];
        if ($root <= 0 || !IPS_ObjectExists($root)) {
            return $result;
        }

        $queue = [[$root, 0]];
        while ($queue !== []) {
            [$parent, $depth] = array_shift($queue);
            foreach (IPS_GetChildrenIDs($parent) as $child) {
                $object = IPS_GetObject($child);
                $type = (int) ($object['ObjectType'] ?? -1);
                if ($type === 2) {
                    $ident = trim((string) ($object['ObjectIdent'] ?? ''));
                    if ($ident !== '') {
                        $result['ident'][strtolower($ident)] = $child;
                    }

                    $nameKey = $this->normalizeLookupKey(IPS_GetName($child));
                    if ($nameKey !== '') {
                        $result['name'][$nameKey] = $child;
                    }
                } elseif ($depth < 2 && in_array($type, [0, 1, 3], true)) {
                    $queue[] = [$child, $depth + 1];
                }
            }
        }

        return $result;
    }

    private function normalizeLookupKey(string $value): string
    {
        $value = strtr($value, [
            'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss'
        ]);
        $value = strtolower(trim($value));
        return preg_replace('/[^a-z0-9]+/', '', $value) ?? '';
    }

    private function assignmentStatus(array $resolved, array $automatic): array
    {
        $status = [];
        foreach (self::ROLE_PROPERTIES as $role => $property) {
            $manual = $this->ReadPropertyInteger($property);
            $autoId = (int) ($automatic[$role] ?? 0);
            $effectiveId = (int) ($resolved[$role] ?? 0);

            if ($manual > 0 && IPS_VariableExists($manual)) {
                $mode = 'manual';
                $effectiveId = $manual;
            } elseif ($effectiveId > 0 && IPS_VariableExists($effectiveId)) {
                $mode = 'auto';
            } else {
                $mode = 'missing';
                $effectiveId = 0;
            }

            $status[$role] = [
                'mode' => $mode,
                'id' => $effectiveId,
                'autoId' => $autoId,
                'name' => $effectiveId > 0 ? IPS_GetName($effectiveId) : ''
            ];
        }
        return $status;
    }

    private function patchAssignmentCaptions(array &$form, array $status): void
    {
        $propertyToRole = array_flip(self::ROLE_PROPERTIES);
        $walk = function (array &$items) use (&$walk, $status, $propertyToRole): void {
            foreach ($items as &$item) {
                if (!is_array($item)) {
                    continue;
                }

                $name = (string) ($item['name'] ?? '');
                if (($item['type'] ?? '') === 'SelectVariable' && isset($propertyToRole[$name])) {
                    $role = $propertyToRole[$name];
                    $mode = (string) ($status[$role]['mode'] ?? 'missing');
                    $marker = $mode === 'auto' ? '🟢' : ($mode === 'manual' ? '🔵' : '🟠');
                    $autoId = (int) ($status[$role]['autoId'] ?? 0);
                    $autoHint = $autoId > 0 ? ' (Auto #' . $autoId . ')' : ' (Auto —)';
                    $item['caption'] = $marker . ' ' . $this->Translate(self::ROLE_LABELS[$role] ?? $role) . $autoHint;
                }

                if (isset($item['items']) && is_array($item['items'])) {
                    $walk($item['items']);
                }
            }
        };
        $walk($form['elements']);

        foreach ($form['elements'] as &$element) {
            if (($element['type'] ?? '') !== 'ExpansionPanel') {
                continue;
            }
            if (($element['caption'] ?? '') === 'Manual variable assignment') {
                $missing = 0;
                foreach ($status as $entry) {
                    if (($entry['mode'] ?? '') === 'missing') {
                        $missing++;
                    }
                }
                $element['expanded'] = $missing > 0;
            }
        }
    }

    private function insertAssignmentStatusPanel(array &$form, array $status): void
    {
        $counts = ['auto' => 0, 'manual' => 0, 'missing' => 0];
        foreach ($status as $entry) {
            $mode = (string) ($entry['mode'] ?? 'missing');
            if (isset($counts[$mode])) {
                $counts[$mode]++;
            }
        }

        $source = $this->ReadPropertyInteger('SourceInstanceID');
        $sourceText = ($source > 0 && IPS_ObjectExists($source))
            ? IPS_GetName($source) . ' (#' . $source . ')'
            : $this->Translate('No vehicle instance selected');

        $panel = [
            'type' => 'ExpansionPanel',
            'caption' => $this->Translate('Assignment status'),
            'expanded' => true,
            'items' => [
                ['type' => 'Label', 'caption' => $this->Translate('Source') . ': ' . $sourceText],
                ['type' => 'Label', 'caption' => '🟢 ' . $this->Translate('Automatically detected') . ': ' . $counts['auto']],
                ['type' => 'Label', 'caption' => '🔵 ' . $this->Translate('Manually assigned') . ': ' . $counts['manual']],
                ['type' => 'Label', 'caption' => '🟠 ' . $this->Translate('Missing') . ': ' . $counts['missing']]
            ]
        ];

        array_splice($form['elements'], 1, 0, [$panel]);
    }

    private function readResolvedVariables(): array
    {
        $decoded = json_decode($this->ReadAttributeString('ResolvedVariables'), true);
        return is_array($decoded) ? array_map('intval', $decoded) : [];
    }

    private function hasUsefulData(array $resolved): bool
    {
        foreach (['soc', 'range', 'mileage', 'charging', 'locked'] as $role) {
            if (($resolved[$role] ?? 0) > 0) {
                return true;
            }
        }
        return false;
    }

    private function pushState(): void
    {
        $this->UpdateVisualizationValue(json_encode($this->buildState(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function buildState(): array
    {
        $ids = $this->resolveVariables();
        $source = $this->ReadPropertyInteger('SourceInstanceID');
        $fallbackName = $source > 0 && IPS_ObjectExists($source) ? IPS_GetName($source) : 'Elektrofahrzeug';

        return [
            'title' => $this->stringValue($ids, 'vehicleName', $fallbackName),
            'licensePlate' => $this->stringValue($ids, 'licensePlate', ''),
            'range' => $this->formatted($ids, 'range'),
            'mileage' => $this->formatted($ids, 'mileage'),
            'lastUpdate' => $this->numericValue($ids, 'lastUpdate'),
            'soc' => $this->numericValue($ids, 'soc'),
            'targetSoc' => $this->numericValue($ids, 'targetSoc'),
            'targetSocFormatted' => $this->formatted($ids, 'targetSoc'),
            'locked' => $this->boolValue($ids, 'locked'),
            'doorsOpen' => $this->boolValue($ids, 'doorsOpen'),
            'windowsOpen' => $this->boolValue($ids, 'windowsOpen'),
            'trunkOpen' => $this->boolValue($ids, 'trunkOpen'),
            'bonnetOpen' => $this->boolValue($ids, 'bonnetOpen'),
            'sunroofOpen' => $this->boolValue($ids, 'sunroofOpen'),
            'lightsOn' => $this->boolValue($ids, 'lightsOn'),
            'charging' => $this->boolValue($ids, 'charging'),
            'chargingState' => $this->stringValue($ids, 'chargingState', ''),
            'chargeType' => $this->stringValue($ids, 'chargeType', ''),
            'chargePower' => $this->formatted($ids, 'chargePower'),
            'chargeMode' => $this->formatted($ids, 'chargeMode'),
            'fullyChargedAt' => $this->numericValue($ids, 'fullyChargedAt'),
            'climate' => $this->boolValue($ids, 'climate'),
            'targetTemperature' => $this->formatted($ids, 'targetTemperature'),
            'parkingState' => $this->stringValue($ids, 'parkingState', ''),
            'controls' => [
                'charging' => $this->canControl($ids['charging'] ?? 0),
                'climate' => $this->canControl($ids['climate'] ?? 0),
                'targetSoc' => $this->canControl($ids['targetSoc'] ?? 0),
                'targetTemperature' => $this->canControl($ids['targetTemperature'] ?? 0)
            ]
        ];
    }

    private function idFor(array $ids, string $role): int
    {
        $id = (int) ($ids[$role] ?? 0);
        return $id > 0 && IPS_VariableExists($id) ? $id : 0;
    }

    private function raw(array $ids, string $role): mixed
    {
        $id = $this->idFor($ids, $role);
        return $id > 0 ? GetValue($id) : null;
    }

    private function formatted(array $ids, string $role): string
    {
        $id = $this->idFor($ids, $role);
        if ($id <= 0) {
            return '';
        }
        try {
            return (string) GetValueFormatted($id);
        } catch (Throwable) {
            $value = GetValue($id);
            return is_scalar($value) ? (string) $value : '';
        }
    }

    private function stringValue(array $ids, string $role, string $default): string
    {
        $value = $this->raw($ids, $role);
        return $value === null ? $default : (string) $value;
    }

    private function numericValue(array $ids, string $role): int|float|null
    {
        $value = $this->raw($ids, $role);
        return is_int($value) || is_float($value) ? $value : null;
    }

    private function boolValue(array $ids, string $role): ?bool
    {
        $value = $this->raw($ids, $role);
        return is_bool($value) ? $value : null;
    }

    private function canControl(int $id): bool
    {
        if (!$this->ReadPropertyBoolean('EnableControls') || $id <= 0 || !IPS_VariableExists($id)) {
            return false;
        }
        $variable = IPS_GetVariable($id);
        return ((int) ($variable['VariableCustomAction'] ?? 0) > 0) || ((int) ($variable['VariableAction'] ?? 0) > 0);
    }

    private function requestRoleAction(string $role, bool|int|float $value): void
    {
        $ids = $this->resolveVariables();
        $id = (int) ($ids[$role] ?? 0);
        if (!$this->canControl($id)) {
            return;
        }
        RequestAction($id, $value);
    }
}
