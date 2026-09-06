<?php

declare(strict_types=1);

trait EVTileMappingTrait
{
    private function groupDefinitions(): array
    {
        return [
            'vehicle' => ['label' => 'Vehicle', 'icon' => 'Car', 'position' => 10],
            'status' => ['label' => 'Status', 'icon' => 'Shield', 'position' => 20],
            'charging' => ['label' => 'Charging', 'icon' => 'Electricity', 'position' => 30],
            'climate' => ['label' => 'Climate', 'icon' => 'Temperature', 'position' => 40],
            'location' => ['label' => 'Location', 'icon' => 'Location', 'position' => 50],
            'diagnostics' => ['label' => 'Diagnostics', 'icon' => 'Information', 'position' => 60]
        ];
    }

    private function roleDefinitions(): array
    {
        return [
            'vehicleName' => ['ident' => 'VehicleName', 'label' => 'Vehicle name', 'group' => 'vehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 10, 'icon' => 'Car'],
            'licensePlate' => ['ident' => 'LicensePlate', 'label' => 'License plate', 'group' => 'vehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 20, 'icon' => 'Information'],
            'range' => ['ident' => 'Range', 'label' => 'Range', 'group' => 'vehicle', 'types' => [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], 'position' => 30, 'icon' => 'Gauge'],
            'mileage' => ['ident' => 'Mileage', 'label' => 'Mileage', 'group' => 'vehicle', 'types' => [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], 'position' => 40, 'icon' => 'Gauge'],
            'parkingState' => ['ident' => 'ParkingState', 'label' => 'Parking state', 'group' => 'vehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 50, 'icon' => 'Car'],
            'lastUpdate' => ['ident' => 'LastUpdate', 'label' => 'Last update', 'group' => 'vehicle', 'types' => [VARIABLETYPE_INTEGER], 'position' => 60, 'icon' => 'Clock'],

            'locked' => ['ident' => 'Locked', 'label' => 'Locked', 'group' => 'status', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 10, 'icon' => 'Lock'],
            'doorsOpen' => ['ident' => 'DoorsOpen', 'label' => 'Doors open', 'group' => 'status', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 20, 'icon' => 'Car'],
            'windowsOpen' => ['ident' => 'WindowsOpen', 'label' => 'Windows open', 'group' => 'status', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 30, 'icon' => 'Car'],
            'trunkOpen' => ['ident' => 'TrunkOpen', 'label' => 'Trunk open', 'group' => 'status', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 40, 'icon' => 'Car'],
            'bonnetOpen' => ['ident' => 'BonnetOpen', 'label' => 'Bonnet open', 'group' => 'status', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 50, 'icon' => 'Car'],
            'sunroofOpen' => ['ident' => 'SunroofOpen', 'label' => 'Sunroof open', 'group' => 'status', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 60, 'icon' => 'Car'],
            'lightsOn' => ['ident' => 'LightsOn', 'label' => 'Lights on', 'group' => 'status', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 70, 'icon' => 'Bulb'],

            'soc' => ['ident' => 'StateOfCharge', 'label' => 'State of charge', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], 'position' => 10, 'icon' => 'Battery'],
            'charging' => ['ident' => 'Charging', 'label' => 'Charging', 'group' => 'charging', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 20, 'icon' => 'Electricity'],
            'chargingState' => ['ident' => 'ChargingState', 'label' => 'Charging state', 'group' => 'charging', 'types' => [VARIABLETYPE_STRING], 'position' => 30, 'icon' => 'Electricity'],
            'chargeType' => ['ident' => 'ChargeType', 'label' => 'Charge type', 'group' => 'charging', 'types' => [VARIABLETYPE_STRING], 'position' => 40, 'icon' => 'Electricity'],
            'chargePower' => ['ident' => 'ChargePower', 'label' => 'Charging power', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], 'position' => 50, 'icon' => 'Electricity'],
            'targetSoc' => ['ident' => 'TargetSOC', 'label' => 'Charging limit', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], 'position' => 60, 'icon' => 'Battery'],
            'chargeMode' => ['ident' => 'ChargeMode', 'label' => 'Charging mode', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER, VARIABLETYPE_STRING], 'position' => 70, 'icon' => 'Gear'],
            'fullyChargedAt' => ['ident' => 'FullyChargedAt', 'label' => 'Fully charged at', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER], 'position' => 80, 'icon' => 'Clock'],

            'climate' => ['ident' => 'Climate', 'label' => 'Air conditioning', 'group' => 'climate', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 10, 'icon' => 'Temperature'],
            'targetTemperature' => ['ident' => 'TargetTemperature', 'label' => 'Target temperature', 'group' => 'climate', 'types' => [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], 'position' => 20, 'icon' => 'Temperature'],

            'latitude' => ['ident' => 'Latitude', 'label' => 'Latitude', 'group' => 'location', 'types' => [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], 'position' => 10, 'icon' => 'Location'],
            'longitude' => ['ident' => 'Longitude', 'label' => 'Longitude', 'group' => 'location', 'types' => [VARIABLETYPE_INTEGER, VARIABLETYPE_FLOAT], 'position' => 20, 'icon' => 'Location'],

            'apiKeyWarning' => ['ident' => 'ApiKeyWarning', 'label' => 'API key warning', 'group' => 'diagnostics', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 10, 'icon' => 'Warning'],
            'apiKeyExpiresAt' => ['ident' => 'ApiKeyExpiresAtVar', 'label' => 'API key valid until', 'group' => 'diagnostics', 'types' => [VARIABLETYPE_INTEGER], 'position' => 20, 'icon' => 'Key'],
            'requestsRemaining' => ['ident' => 'RequestsRemaining', 'label' => 'API requests remaining', 'group' => 'diagnostics', 'types' => [VARIABLETYPE_INTEGER], 'position' => 30, 'icon' => 'Gauge'],
            'partialErrors' => ['ident' => 'PartialErrors', 'label' => 'API partial errors', 'group' => 'diagnostics', 'types' => [VARIABLETYPE_STRING], 'position' => 40, 'icon' => 'Warning'],
            'newApiFeatures' => ['ident' => 'NewApiFeatures', 'label' => 'New API functions', 'group' => 'diagnostics', 'types' => [VARIABLETYPE_INTEGER], 'position' => 50, 'icon' => 'Information']
        ];
    }

    private function manualPropertyName(array $definition): string
    {
        return 'Map_' . (string) $definition['ident'];
    }

    private function resolveVariables(): array
    {
        $automatic = $this->resolveAutomaticCandidates();
        $resolved = [];

        foreach ($this->roleDefinitions() as $role => $definition) {
            $manualId = $this->ReadPropertyInteger($this->manualPropertyName($definition));
            if ($this->isValidVariableForRole($manualId, $definition)) {
                $resolved[$role] = $manualId;
                continue;
            }

            $validAutomatic = $this->validCandidatesForRole($automatic[$role] ?? [], $definition);
            $resolved[$role] = count($validAutomatic) === 1 ? (int) $validAutomatic[0] : 0;
        }

        return $resolved;
    }

    private function resolveAutomaticCandidates(): array
    {
        $catalog = $this->collectVariableCatalog($this->ReadPropertyInteger('SourceInstanceID'));
        $result = [];

        foreach ($this->roleDefinitions() as $role => $definition) {
            $ident = (string) $definition['ident'];
            $result[$role] = array_values(array_unique(array_map('intval', $catalog[$ident] ?? [])));
        }

        return $result;
    }

    private function collectVariableCatalog(int $root): array
    {
        $result = [];
        if ($root <= 0 || !IPS_ObjectExists($root)) {
            return $result;
        }

        $queue = [[$root, 0]];
        while ($queue !== []) {
            [$parentId, $depth] = array_shift($queue);
            foreach (IPS_GetChildrenIDs((int) $parentId) as $childId) {
                $object = IPS_GetObject($childId);
                $objectType = (int) ($object['ObjectType'] ?? -1);

                if ($objectType === 2) {
                    $ident = (string) ($object['ObjectIdent'] ?? '');
                    if ($ident !== '') {
                        $result[$ident][] = (int) $childId;
                    }
                    continue;
                }

                if ($depth < 2 && in_array($objectType, [0, 1], true)) {
                    $queue[] = [(int) $childId, $depth + 1];
                }
            }
        }

        return $result;
    }

    private function validCandidatesForRole(array $candidates, array $definition): array
    {
        return array_values(array_filter(
            array_map('intval', $candidates),
            fn (int $id): bool => $this->isValidVariableForRole($id, $definition)
        ));
    }

    private function isValidVariableForRole(int $id, array $definition): bool
    {
        if ($id <= 0 || !IPS_VariableExists($id)) {
            return false;
        }

        $variable = IPS_GetVariable($id);
        return in_array((int) ($variable['VariableType'] ?? -1), (array) $definition['types'], true);
    }

    private function assignmentStatus(): array
    {
        $automatic = $this->resolveAutomaticCandidates();
        $resolved = $this->resolveVariables();
        $status = [];

        foreach ($this->roleDefinitions() as $role => $definition) {
            $manualId = $this->ReadPropertyInteger($this->manualPropertyName($definition));
            $manualValid = $this->isValidVariableForRole($manualId, $definition);
            $validAutomatic = $this->validCandidatesForRole($automatic[$role] ?? [], $definition);
            $effectiveId = (int) ($resolved[$role] ?? 0);

            if ($manualValid) {
                $mode = 'manual';
            } elseif (count($validAutomatic) === 1) {
                $mode = 'auto';
            } else {
                $mode = 'missing';
            }

            $reason = '';
            if ($mode === 'missing') {
                if ($manualId > 0 && !$manualValid) {
                    $reason = 'manual_invalid';
                } elseif (count($validAutomatic) > 1) {
                    $reason = 'ambiguous';
                } elseif (($automatic[$role] ?? []) !== [] && $validAutomatic === []) {
                    $reason = 'wrong_type';
                }
            }

            $status[$role] = [
                'mode' => $mode,
                'reason' => $reason,
                'id' => $effectiveId,
                'name' => $effectiveId > 0 && IPS_ObjectExists($effectiveId) ? IPS_GetName($effectiveId) : '',
                'automaticCandidates' => $validAutomatic
            ];
        }

        return $status;
    }

    private function hasAnyMappedData(array $resolved): bool
    {
        foreach ($resolved as $id) {
            if ((int) $id > 0) {
                return true;
            }
        }
        return false;
    }

    private function idForRole(array $resolved, string $role): int
    {
        $id = (int) ($resolved[$role] ?? 0);
        return $id > 0 && IPS_VariableExists($id) ? $id : 0;
    }

    private function rawRoleValue(array $resolved, string $role): mixed
    {
        $id = $this->idForRole($resolved, $role);
        return $id > 0 ? GetValue($id) : null;
    }

    private function formattedRoleValue(array $resolved, string $role): string
    {
        $id = $this->idForRole($resolved, $role);
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

    private function stringRoleValue(array $resolved, string $role, string $default = ''): string
    {
        $value = $this->rawRoleValue($resolved, $role);
        return $value === null ? $default : (string) $value;
    }

    private function numericRoleValue(array $resolved, string $role): int|float|null
    {
        $value = $this->rawRoleValue($resolved, $role);
        return is_int($value) || is_float($value) ? $value : null;
    }

    private function boolRoleValue(array $resolved, string $role): ?bool
    {
        $value = $this->rawRoleValue($resolved, $role);
        return is_bool($value) ? $value : null;
    }
}
