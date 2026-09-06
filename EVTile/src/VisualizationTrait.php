<?php

declare(strict_types=1);

trait EVTileVisualizationTrait
{
    public function MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void
    {
        if ($Message !== VM_UPDATE) {
            return;
        }

        if (in_array($SenderID, array_values($this->readResolvedVariables()), true)) {
            $this->pushVisualizationState();
        }
    }

    public function GetVisualizationTile(): string
    {
        $html = (string) @file_get_contents(__DIR__ . '/../module.html');
        $state = json_encode(
            $this->buildVisualizationState(),
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
        );

        if (!is_string($state)) {
            $state = '{}';
        }

        return $html . '<script>if(typeof handleMessage==="function"){handleMessage(' . $state . ');}</script>';
    }

    private function pushVisualizationState(): void
    {
        $payload = json_encode(
            $this->buildVisualizationState(),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if (is_string($payload)) {
            $this->UpdateVisualizationValue($payload);
        }
    }

    private function buildVisualizationState(): array
    {
        $resolved = $this->resolveVariables();
        $sourceId = $this->ReadPropertyInteger('SourceInstanceID');
        $fallbackTitle = $sourceId > 0 && IPS_ObjectExists($sourceId)
            ? IPS_GetName($sourceId)
            : $this->Translate('Vehicle');

        return [
            'available' => $this->hasAnyMappedData($resolved),
            'vehicle' => [
                'name' => $this->stringRoleValue($resolved, 'vehicleName', $fallbackTitle),
                'plate' => $this->stringRoleValue($resolved, 'licensePlate'),
                'range' => $this->formattedRoleValue($resolved, 'range'),
                'mileage' => $this->formattedRoleValue($resolved, 'mileage'),
                'parkingState' => $this->formattedRoleValue($resolved, 'parkingState'),
                'lastUpdate' => $this->numericRoleValue($resolved, 'lastUpdate')
            ],
            'battery' => [
                'soc' => $this->numericRoleValue($resolved, 'soc'),
                'socFormatted' => $this->formattedRoleValue($resolved, 'soc')
            ],
            'status' => [
                'locked' => $this->boolRoleValue($resolved, 'locked'),
                'doorsOpen' => $this->boolRoleValue($resolved, 'doorsOpen'),
                'windowsOpen' => $this->boolRoleValue($resolved, 'windowsOpen'),
                'trunkOpen' => $this->boolRoleValue($resolved, 'trunkOpen'),
                'bonnetOpen' => $this->boolRoleValue($resolved, 'bonnetOpen'),
                'sunroofOpen' => $this->boolRoleValue($resolved, 'sunroofOpen'),
                'lightsOn' => $this->boolRoleValue($resolved, 'lightsOn')
            ],
            'charging' => [
                'active' => $this->boolRoleValue($resolved, 'charging'),
                'state' => $this->formattedRoleValue($resolved, 'chargingState'),
                'type' => $this->formattedRoleValue($resolved, 'chargeType'),
                'power' => $this->formattedRoleValue($resolved, 'chargePower'),
                'targetSoc' => $this->formattedRoleValue($resolved, 'targetSoc'),
                'mode' => $this->formattedRoleValue($resolved, 'chargeMode'),
                'fullyChargedAt' => $this->numericRoleValue($resolved, 'fullyChargedAt')
            ],
            'climate' => [
                'active' => $this->boolRoleValue($resolved, 'climate'),
                'targetTemperature' => $this->formattedRoleValue($resolved, 'targetTemperature')
            ],
            'location' => [
                'latitude' => $this->numericRoleValue($resolved, 'latitude'),
                'longitude' => $this->numericRoleValue($resolved, 'longitude')
            ],
            'diagnostics' => [
                'apiKeyWarning' => $this->boolRoleValue($resolved, 'apiKeyWarning'),
                'newApiFeatures' => $this->numericRoleValue($resolved, 'newApiFeatures'),
                'partialErrors' => $this->stringRoleValue($resolved, 'partialErrors')
            ]
        ];
    }
}
