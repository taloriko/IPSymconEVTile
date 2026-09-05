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

        $previous = $this->readResolvedVariables();
        foreach (array_unique(array_values($previous)) as $id) {
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
        $byIdent = $this->collectVariablesByIdent($this->ReadPropertyInteger('SourceInstanceID'));
        $resolved = [];

        foreach (self::ROLE_PROPERTIES as $role => $property) {
            $manual = $this->ReadPropertyInteger($property);
            if ($manual > 0 && IPS_VariableExists($manual)) {
                $resolved[$role] = $manual;
                continue;
            }

            $resolved[$role] = 0;
            foreach (self::ROLE_ALIASES[$role] ?? [] as $alias) {
                $key = strtolower($alias);
                if (isset($byIdent[$key])) {
                    $resolved[$role] = $byIdent[$key];
                    break;
                }
            }
        }

        return $resolved;
    }

    private function collectVariablesByIdent(int $root): array
    {
        if ($root <= 0 || !IPS_ObjectExists($root)) {
            return [];
        }

        $result = [];
        $queue = [[$root, 0]];
        while ($queue !== []) {
            [$parent, $depth] = array_shift($queue);
            foreach (IPS_GetChildrenIDs($parent) as $child) {
                $object = IPS_GetObject($child);
                if (($object['ObjectType'] ?? -1) === 2) {
                    $ident = trim((string) ($object['ObjectIdent'] ?? ''));
                    if ($ident !== '') {
                        $result[strtolower($ident)] = $child;
                    }
                } elseif ($depth < 1 && in_array(($object['ObjectType'] ?? -1), [0, 1], true)) {
                    $queue[] = [$child, $depth + 1];
                }
            }
        }
        return $result;
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
        $ids = $this->readResolvedVariables();
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
        $ids = $this->readResolvedVariables();
        $id = (int) ($ids[$role] ?? 0);
        if (!$this->canControl($id)) {
            return;
        }
        RequestAction($id, $value);
    }
}
