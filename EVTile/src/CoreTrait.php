<?php

declare(strict_types=1);

trait EVTileCoreTrait
{
    public function Create(): void
    {
        parent::Create();

        $this->RegisterPropertyInteger('SourceInstanceID', 0);
        $this->RegisterPropertyBoolean('CreateListView', false);

        foreach ($this->roleDefinitions() as $definition) {
            $this->RegisterPropertyInteger($this->manualPropertyName($definition), 0);
        }

        $this->RegisterAttributeString('ResolvedVariables', '{}');
        $this->RegisterAttributeString('ManagedLinkTargets', '{}');
        $this->SetVisualizationType(1);
    }

    public function ApplyChanges(): void
    {
        parent::ApplyChanges();
        $this->SetVisualizationType(1);

        foreach (array_unique(array_values($this->readResolvedVariables())) as $id) {
            if ($id > 0 && IPS_ObjectExists($id)) {
                $this->UnregisterMessage($id, VM_UPDATE);
            }
        }

        foreach ($this->GetReferenceList() as $referenceId) {
            $this->UnregisterReference($referenceId);
        }

        $sourceId = $this->ReadPropertyInteger('SourceInstanceID');
        if ($sourceId > 0 && IPS_ObjectExists($sourceId)) {
            $this->RegisterReference($sourceId);
        }

        $resolved = $this->resolveVariables();
        foreach (array_unique(array_values($resolved)) as $id) {
            if ($id <= 0 || !IPS_VariableExists($id)) {
                continue;
            }
            $this->RegisterReference($id);
            $this->RegisterMessage($id, VM_UPDATE);
        }

        $this->WriteAttributeString(
            'ResolvedVariables',
            json_encode($resolved, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        if ($this->ReadPropertyBoolean('CreateListView')) {
            $this->maintainObjectTree($resolved);
        }

        if ($sourceId <= 0) {
            $this->SetStatus(104);
        } elseif (!IPS_ObjectExists($sourceId)) {
            $this->SetStatus(201);
        } elseif (!$this->hasAnyMappedData($resolved)) {
            $this->SetStatus(202);
        } else {
            $this->SetStatus(102);
        }

        if (IPS_GetKernelRunlevel() === KR_READY) {
            $this->pushVisualizationState();
        }
    }

    public function GetConfigurationForm(): string
    {
        $json = @file_get_contents(__DIR__ . '/../form.json');
        if (!is_string($json) || $json === '') {
            return '{}';
        }

        $form = json_decode($json, true);
        if (!is_array($form)) {
            return '{}';
        }

        $status = $this->assignmentStatus();
        $this->insertAssignmentStatusPanel($form, $status);
        $this->insertManualAssignmentPanel($form, $status);

        return json_encode($form, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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

        $sourceId = $this->ReadPropertyInteger('SourceInstanceID');
        if ($sourceId > 0 && IPS_ObjectExists($sourceId)) {
            $sourceText = IPS_GetName($sourceId) . ' (#' . $sourceId . ')';
        } elseif ($sourceId > 0) {
            $sourceText = $this->Translate('Selected vehicle instance does not exist');
        } else {
            $sourceText = $this->Translate('No vehicle instance selected');
        }

        $available = $counts['auto'] + $counts['manual'];
        $total = count($this->roleDefinitions());

        $items = [
            ['type' => 'Label', 'caption' => $this->Translate('Source') . ': ' . $sourceText],
            ['type' => 'Label', 'caption' => '🟢 ' . $this->Translate('Automatically detected') . ': ' . $counts['auto']],
            ['type' => 'Label', 'caption' => '🔵 ' . $this->Translate('Manually assigned') . ': ' . $counts['manual']],
            ['type' => 'Label', 'caption' => '🟠 ' . $this->Translate('Missing') . ': ' . $counts['missing']],
            ['type' => 'Label', 'caption' => sprintf($this->Translate('%d of %d data points available'), $available, $total)]
        ];

        foreach ($this->groupDefinitions() as $groupKey => $group) {
            $groupLines = [];
            foreach ($this->roleDefinitions() as $role => $definition) {
                if ($definition['group'] !== $groupKey) {
                    continue;
                }

                $entry = $status[$role] ?? ['mode' => 'missing', 'reason' => '', 'id' => 0, 'name' => ''];
                $marker = match ($entry['mode']) {
                    'auto' => '🟢',
                    'manual' => '🔵',
                    default => '🟠'
                };

                $target = $entry['id'] > 0
                    ? '#' . $entry['id'] . ' ' . $entry['name']
                    : $this->Translate('missing');

                $reason = match ($entry['reason'] ?? '') {
                    'ambiguous' => ' (' . $this->Translate('ambiguous') . ')',
                    'wrong_type' => ' (' . $this->Translate('wrong variable type') . ')',
                    'manual_invalid' => ' (' . $this->Translate('manual selection invalid') . ')',
                    default => ''
                };

                $groupLines[] = [
                    'type' => 'Label',
                    'caption' => $marker . ' ' . $this->Translate((string) $definition['label'])
                        . ' · ' . $definition['ident'] . ' · ' . $target . $reason
                ];
            }

            if ($groupLines !== []) {
                $items[] = ['type' => 'Label', 'caption' => '— ' . $this->Translate((string) $group['label']) . ' —'];
                array_push($items, ...$groupLines);
            }
        }

        $panel = [
            'type' => 'ExpansionPanel',
            'caption' => $this->Translate('Assignment status'),
            'expanded' => true,
            'items' => $items
        ];

        array_splice($form['elements'], 1, 0, [$panel]);
    }

    private function insertManualAssignmentPanel(array &$form, array $status): void
    {
        $items = [
            [
                'type' => 'Label',
                'caption' => $this->Translate('Manual assignments override automatic Ident mapping only for the selected data point. Invalid manual selections fall back to automatic mapping.')
            ]
        ];

        foreach ($this->groupDefinitions() as $groupKey => $group) {
            $groupFields = [];
            foreach ($this->roleDefinitions() as $role => $definition) {
                if ($definition['group'] !== $groupKey) {
                    continue;
                }

                $entry = $status[$role] ?? ['mode' => 'missing', 'id' => 0];
                $marker = match ($entry['mode']) {
                    'auto' => '🟢',
                    'manual' => '🔵',
                    default => '🟠'
                };

                $autoHint = '';
                if ($entry['mode'] === 'auto' && $entry['id'] > 0) {
                    $autoHint = ' (Auto #' . $entry['id'] . ')';
                }

                $groupFields[] = [
                    'type' => 'SelectVariable',
                    'name' => $this->manualPropertyName($definition),
                    'caption' => $marker . ' ' . $this->Translate((string) $definition['label']) . $autoHint,
                    'validVariableTypes' => array_values($definition['types'])
                ];
            }

            if ($groupFields === []) {
                continue;
            }

            $items[] = ['type' => 'Label', 'caption' => '— ' . $this->Translate((string) $group['label']) . ' —'];
            foreach (array_chunk($groupFields, 3) as $row) {
                $items[] = ['type' => 'RowLayout', 'items' => $row];
            }
        }

        $missing = 0;
        foreach ($status as $entry) {
            if (($entry['mode'] ?? '') === 'missing') {
                $missing++;
            }
        }

        $panel = [
            'type' => 'ExpansionPanel',
            'caption' => $this->Translate('Manual variable assignment'),
            'expanded' => $missing > 0,
            'items' => $items
        ];

        $insertIndex = count($form['elements']);
        foreach ($form['elements'] as $index => $element) {
            if (($element['caption'] ?? '') === 'Help and documentation') {
                $insertIndex = $index;
                break;
            }
        }
        array_splice($form['elements'], $insertIndex, 0, [$panel]);
    }

    private function readResolvedVariables(): array
    {
        $decoded = json_decode($this->ReadAttributeString('ResolvedVariables'), true);
        if (!is_array($decoded)) {
            return [];
        }

        $result = [];
        foreach ($decoded as $role => $id) {
            $result[(string) $role] = (int) $id;
        }
        return $result;
    }
}
