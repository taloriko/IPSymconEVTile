<?php

declare(strict_types=1);

trait EVTileChartTrait
{
    private function maintainChargingChart(array $resolved): void
    {
        if (!$this->ReadPropertyBoolean('CreateChargingChart')) {
            return;
        }

        $socId = (int) ($resolved['soc'] ?? 0);
        $targetSocId = (int) ($resolved['targetSoc'] ?? 0);
        $chargePowerId = (int) ($resolved['chargePower'] ?? 0);

        if (!$this->validChartVariable($socId) || !$this->validChartVariable($targetSocId) || !$this->validChartVariable($chargePowerId)) {
            $this->LogMessage(
                'EV Tile: charging chart requires StateOfCharge, TargetSOC and ChargePower.',
                KL_WARNING
            );
            return;
        }

        $groups = $this->groupDefinitions();
        $chartGroup = $groups['charts'] ?? null;
        if (!is_array($chartGroup)) {
            return;
        }

        $groupId = $this->ensureGroup('charts', $chartGroup);
        if ($groupId <= 0) {
            return;
        }

        $ident = 'EVTILE_Chart_Charging';
        $existingId = $this->findChildByIdent($groupId, $ident);
        if ($existingId > 0) {
            $object = IPS_GetObject($existingId);
            if ((int) ($object['ObjectType'] ?? -1) !== OBJECTTYPE_MEDIA) {
                $this->LogMessage('EV Tile: Ident ' . $ident . ' is already used by another object.', KL_WARNING);
                return;
            }

            $media = IPS_GetMedia($existingId);
            if ((int) ($media['MediaType'] ?? -1) !== MEDIATYPE_CHART) {
                $this->LogMessage('EV Tile: existing media for charging chart is not a chart.', KL_WARNING);
            }
            return;
        }

        $chartId = IPS_CreateMedia(MEDIATYPE_CHART);
        IPS_SetParent($chartId, $groupId);
        IPS_SetIdent($chartId, $ident);
        IPS_SetName($chartId, $this->Translate('Charging overview'));
        IPS_SetIcon($chartId, 'Graph');
        IPS_SetPosition($chartId, 10);
        IPS_SetMediaCached($chartId, false);

        $path = IPS_GetKernelDir()
            . 'media'
            . DIRECTORY_SEPARATOR
            . $chartId
            . '.chart';
        IPS_SetMediaFile($chartId, $path, false);

        $chart = [
            'datasets' => [
                [
                    'variableID' => $socId,
                    'fillColor' => 'clear',
                    'strokeColor' => '#2563EB',
                    'timeOffset' => 0,
                    'title' => '',
                    'axis' => 0
                ],
                [
                    'variableID' => $targetSocId,
                    'fillColor' => 'clear',
                    'strokeColor' => '#22C55E',
                    'timeOffset' => 0,
                    'title' => '',
                    'axis' => 0
                ],
                [
                    'variableID' => $chargePowerId,
                    'fillColor' => 'clear',
                    'strokeColor' => '#F59E0B',
                    'timeOffset' => 0,
                    'title' => '',
                    'axis' => 1
                ]
            ],
            'type' => 'line',
            'axes' => [
                [
                    'profile' => '~Battery.100',
                    'side' => 'left'
                ],
                [
                    'profile' => '~Power',
                    'side' => 'right'
                ]
            ]
        ];

        $json = json_encode($chart, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($json)) {
            return;
        }

        IPS_SetMediaContent($chartId, base64_encode($json));
        IPS_SendMediaEvent($chartId);
    }

    private function validChartVariable(int $id): bool
    {
        return $id > 0 && IPS_VariableExists($id);
    }
}
