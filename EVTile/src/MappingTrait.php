<?php

declare(strict_types=1);

trait EVTileMappingTrait
{
    private function groupDefinitions(): array
    {
        return [
            'overview' => ['label' => 'Overview', 'icon' => 'Car', 'position' => 10],
            'vehicle' => ['label' => 'Vehicle', 'icon' => 'Car', 'position' => 20],
            'airConditioning' => ['label' => 'Air conditioning', 'icon' => 'Temperature', 'position' => 30],
            'charging' => ['label' => 'Charging', 'icon' => 'Electricity', 'position' => 40],
            'odometer' => ['label' => 'Odometer', 'icon' => 'Car', 'position' => 50],
            'parkingPosition' => ['label' => 'Parking position', 'icon' => 'Location', 'position' => 60],
            'statusOverall' => ['label' => 'Vehicle status', 'icon' => 'Shield', 'position' => 70],
            'statusDetail' => ['label' => 'Vehicle details', 'icon' => 'Car', 'position' => 80],
            'module' => ['label' => 'Module data', 'icon' => 'Information', 'position' => 90],
            'vin' => ['label' => 'VIN data', 'icon' => 'Information', 'position' => 100],
            'apiVehicle' => ['label' => 'API vehicle data', 'icon' => 'Information', 'position' => 110],
            'charts' => ['label' => 'Charts', 'icon' => 'Graph', 'position' => 120]
        ];
    }

    private function roleDefinitions(): array
    {
        return [
            // vehicle
            'vehicleName' => ['ident' => 'VehicleName', 'label' => 'Vehicle name', 'group' => 'vehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 10],
            'licensePlate' => ['ident' => 'LicensePlate', 'label' => 'License plate', 'group' => 'vehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 20],
            'vin' => ['ident' => 'VIN', 'label' => 'VIN', 'group' => 'vehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 30],

            // vehicle.airConditioning
            'climateState' => ['ident' => 'ClimateState', 'label' => 'Air conditioning state', 'group' => 'airConditioning', 'types' => [VARIABLETYPE_STRING], 'position' => 10],
            'airConditioningAtUnlock' => ['ident' => 'AirConditioningAtUnlock', 'label' => 'Air conditioning at unlock', 'group' => 'airConditioning', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 20],
            'targetTemperature' => ['ident' => 'TargetTemperature', 'label' => 'Target temperature', 'group' => 'airConditioning', 'types' => [VARIABLETYPE_FLOAT], 'position' => 30],
            'targetTemperatureUnit' => ['ident' => 'TargetTemperatureUnit', 'label' => 'Target temperature unit', 'group' => 'airConditioning', 'types' => [VARIABLETYPE_STRING], 'position' => 40],
            'windowHeatingEnabled' => ['ident' => 'WindowHeatingEnabled', 'label' => 'Window heating enabled', 'group' => 'airConditioning', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 50],
            'windowHeatingFront' => ['ident' => 'WindowHeatingFront', 'label' => 'Front window heating', 'group' => 'airConditioning', 'types' => [VARIABLETYPE_STRING], 'position' => 60],
            'windowHeatingRear' => ['ident' => 'WindowHeatingRear', 'label' => 'Rear window heating', 'group' => 'airConditioning', 'types' => [VARIABLETYPE_STRING], 'position' => 70],

            // vehicle.charging
            'atSavedChargingLocation' => ['ident' => 'AtSavedChargingLocation', 'label' => 'At saved charging location', 'group' => 'charging', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 10],
            'autoUnlockPlug' => ['ident' => 'AutoUnlockPlug', 'label' => 'Automatic plug unlock', 'group' => 'charging', 'types' => [VARIABLETYPE_STRING], 'position' => 20],
            'batteryCareTargetSoc' => ['ident' => 'BatteryCareTargetSOC', 'label' => 'Battery care target', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER], 'position' => 30],
            'batteryCareMode' => ['ident' => 'BatteryCareMode', 'label' => 'Battery care mode', 'group' => 'charging', 'types' => [VARIABLETYPE_STRING], 'position' => 40],
            'maxChargeCurrentAc' => ['ident' => 'MaxChargeCurrentAC', 'label' => 'Maximum AC charging current', 'group' => 'charging', 'types' => [VARIABLETYPE_STRING], 'position' => 50],
            'chargeMode' => ['ident' => 'ChargeMode', 'label' => 'Charging mode', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER], 'position' => 60],
            'targetSoc' => ['ident' => 'TargetSOC', 'label' => 'Charging limit', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER], 'position' => 70],
            'range' => ['ident' => 'Range', 'label' => 'Range', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER], 'position' => 80],
            'soc' => ['ident' => 'StateOfCharge', 'label' => 'State of charge', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER], 'position' => 90],
            'chargePower' => ['ident' => 'ChargePower', 'label' => 'Charging power', 'group' => 'charging', 'types' => [VARIABLETYPE_FLOAT], 'position' => 100],
            'fullyChargedAt' => ['ident' => 'FullyChargedAt', 'label' => 'Fully charged at', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER], 'position' => 110],
            'remainingChargingTime' => ['ident' => 'RemainingChargingTime', 'label' => 'Remaining charging time', 'group' => 'charging', 'types' => [VARIABLETYPE_INTEGER], 'position' => 120],
            'chargingState' => ['ident' => 'ChargingState', 'label' => 'Charging state', 'group' => 'charging', 'types' => [VARIABLETYPE_STRING], 'position' => 130],
            'chargeType' => ['ident' => 'ChargeType', 'label' => 'Charge type', 'group' => 'charging', 'types' => [VARIABLETYPE_STRING], 'position' => 140],

            // vehicle.odometer
            'mileage' => ['ident' => 'Mileage', 'label' => 'Mileage', 'group' => 'odometer', 'types' => [VARIABLETYPE_INTEGER], 'position' => 10],

            // vehicle.parkingPosition
            'parkingState' => ['ident' => 'ParkingState', 'label' => 'Parking state', 'group' => 'parkingPosition', 'types' => [VARIABLETYPE_STRING], 'position' => 10],
            'parkingAddress' => ['ident' => 'ParkingAddress', 'label' => 'Parking address', 'group' => 'parkingPosition', 'types' => [VARIABLETYPE_STRING], 'position' => 20],
            'latitude' => ['ident' => 'Latitude', 'label' => 'Latitude', 'group' => 'parkingPosition', 'types' => [VARIABLETYPE_FLOAT], 'position' => 30],
            'longitude' => ['ident' => 'Longitude', 'label' => 'Longitude', 'group' => 'parkingPosition', 'types' => [VARIABLETYPE_FLOAT], 'position' => 40],

            // vehicle.status.overall
            'doorsLocked' => ['ident' => 'DoorsLocked', 'label' => 'Door lock status', 'group' => 'statusOverall', 'types' => [VARIABLETYPE_STRING], 'position' => 10],
            'locked' => ['ident' => 'Locked', 'label' => 'Vehicle lock status', 'group' => 'statusOverall', 'types' => [VARIABLETYPE_STRING], 'position' => 20],
            'doorsOpen' => ['ident' => 'DoorsOpen', 'label' => 'Doors', 'group' => 'statusOverall', 'types' => [VARIABLETYPE_STRING], 'position' => 30],
            'windowsOpen' => ['ident' => 'WindowsOpen', 'label' => 'Windows', 'group' => 'statusOverall', 'types' => [VARIABLETYPE_STRING], 'position' => 40],
            'lightsOn' => ['ident' => 'LightsOn', 'label' => 'Lights', 'group' => 'statusOverall', 'types' => [VARIABLETYPE_STRING], 'position' => 50],
            'reliableLockStatus' => ['ident' => 'ReliableLockStatus', 'label' => 'Reliable lock status', 'group' => 'statusOverall', 'types' => [VARIABLETYPE_STRING], 'position' => 60],

            // vehicle.status.detail
            'sunroofOpen' => ['ident' => 'SunroofOpen', 'label' => 'Sunroof', 'group' => 'statusDetail', 'types' => [VARIABLETYPE_STRING], 'position' => 10],
            'trunkOpen' => ['ident' => 'TrunkOpen', 'label' => 'Trunk', 'group' => 'statusDetail', 'types' => [VARIABLETYPE_STRING], 'position' => 20],
            'bonnetOpen' => ['ident' => 'BonnetOpen', 'label' => 'Bonnet', 'group' => 'statusDetail', 'types' => [VARIABLETYPE_STRING], 'position' => 30],

            // Module-generated data
            'climate' => ['ident' => 'Climate', 'label' => 'Air conditioning', 'group' => 'module', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 10],
            'charging' => ['ident' => 'Charging', 'label' => 'Charging', 'group' => 'module', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 20],
            'lastUpdate' => ['ident' => 'LastUpdate', 'label' => 'Last update', 'group' => 'module', 'types' => [VARIABLETYPE_INTEGER], 'position' => 30],
            'apiKeyWarning' => ['ident' => 'ApiKeyWarning', 'label' => 'API key warning', 'group' => 'module', 'types' => [VARIABLETYPE_BOOLEAN], 'position' => 40],
            'apiKeyExpiresAt' => ['ident' => 'ApiKeyExpiresAtVar', 'label' => 'API key valid until', 'group' => 'module', 'types' => [VARIABLETYPE_INTEGER], 'position' => 50],
            'requestsRemaining' => ['ident' => 'RequestsRemaining', 'label' => 'API requests remaining', 'group' => 'module', 'types' => [VARIABLETYPE_INTEGER], 'position' => 60],
            'partialErrors' => ['ident' => 'PartialErrors', 'label' => 'API partial errors', 'group' => 'module', 'types' => [VARIABLETYPE_STRING], 'position' => 70],
            'newApiFeatures' => ['ident' => 'NewApiFeatures', 'label' => 'New API functions', 'group' => 'module', 'types' => [VARIABLETYPE_INTEGER], 'position' => 80],
            'pendingCommands' => ['ident' => 'PendingCommands', 'label' => 'Pending commands', 'group' => 'module', 'types' => [VARIABLETYPE_INTEGER], 'position' => 90],
            'commandStatus' => ['ident' => 'CommandStatus', 'label' => 'Command status', 'group' => 'module', 'types' => [VARIABLETYPE_STRING], 'position' => 100],

            // Locally decoded VIN data
            'vinWmi' => ['ident' => 'VINWMI', 'label' => 'VIN WMI', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 10],
            'vinVds' => ['ident' => 'VINVDS', 'label' => 'VIN VDS', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 20],
            'vinVis' => ['ident' => 'VINVIS', 'label' => 'VIN VIS', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 30],
            'vinManufacturer' => ['ident' => 'VINManufacturer', 'label' => 'VIN manufacturer', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 40],
            'vinCountry' => ['ident' => 'VINCountry', 'label' => 'VIN country', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 50],
            'vinModel' => ['ident' => 'VINModel', 'label' => 'VIN model', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 60],
            'vinModelCode' => ['ident' => 'VINModelCode', 'label' => 'VIN model code', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 70],
            'vinBody' => ['ident' => 'VINBody', 'label' => 'VIN body', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 80],
            'vinSteering' => ['ident' => 'VINSteering', 'label' => 'VIN steering', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 90],
            'vinDrive' => ['ident' => 'VINDrive', 'label' => 'VIN drive', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 100],
            'vinPower' => ['ident' => 'VINPower', 'label' => 'VIN power', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 110],
            'vinVariant' => ['ident' => 'VINVariant', 'label' => 'VIN variant', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 120],
            'vinRestraint' => ['ident' => 'VINRestraint', 'label' => 'VIN restraint system', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 130],
            'vinModelYear' => ['ident' => 'VINModelYear', 'label' => 'VIN model year', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 140],
            'vinPlant' => ['ident' => 'VINPlant', 'label' => 'VIN production plant', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 150],
            'vinSerialNumber' => ['ident' => 'VINSerialNumber', 'label' => 'VIN serial number', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 160],
            'vinCheckDigit' => ['ident' => 'VINCheckDigit', 'label' => 'VIN check digit', 'group' => 'vin', 'types' => [VARIABLETYPE_STRING], 'position' => 170],

            // Module-generated API vehicle data
            'apiCarType' => ['ident' => 'APICarType', 'label' => 'API vehicle type', 'group' => 'apiVehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 10],
            'apiPrimaryEngineType' => ['ident' => 'APIPrimaryEngineType', 'label' => 'API primary engine type', 'group' => 'apiVehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 20],
            'apiSecondaryEngineType' => ['ident' => 'APISecondaryEngineType', 'label' => 'API secondary engine type', 'group' => 'apiVehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 30],
            'apiSupportedFeatures' => ['ident' => 'APISupportedFeatures', 'label' => 'API supported features', 'group' => 'apiVehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 40],
            'apiAvailableChargeModes' => ['ident' => 'APIAvailableChargeModes', 'label' => 'API available charging modes', 'group' => 'apiVehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 50],
            'apiRemoteOperations' => ['ident' => 'APIRemoteOperations', 'label' => 'API remote operations', 'group' => 'apiVehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 60],
            'apiAuxiliaryHeatingState' => ['ident' => 'APIAuxiliaryHeatingState', 'label' => 'API auxiliary heating state', 'group' => 'apiVehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 70],
            'apiActiveVentilationState' => ['ident' => 'APIActiveVentilationState', 'label' => 'API active ventilation state', 'group' => 'apiVehicle', 'types' => [VARIABLETYPE_STRING], 'position' => 80]
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

                if ($objectType === OBJECTTYPE_VARIABLE) {
                    $ident = (string) ($object['ObjectIdent'] ?? '');
                    if ($ident !== '') {
                        $result[$ident][] = (int) $childId;
                    }
                    continue;
                }

                if ($depth < 2 && in_array($objectType, [OBJECTTYPE_CATEGORY, OBJECTTYPE_INSTANCE], true)) {
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
}
