<?php

declare(strict_types=1);

trait EVTileObjectTreeTrait
{
    private const DUMMY_MODULE_ID = '{485D0419-BE97-4548-AA9C-C083EB82E61E}';

    private function maintainObjectTree(array $resolved): void
    {
        if (!$this->ReadPropertyBoolean('CreateListView')) {
            return;
        }

        $managedTargets = $this->readManagedLinkTargets();

        foreach ($this->groupDefinitions() as $groupKey => $group) {
            if ($groupKey === 'charts') {
                continue;
            }

            $roles = $groupKey === 'vehicle'
                ? $this->vehicleOverviewRoles()
                : $this->rolesForGroup($groupKey);

            $availableRoles = [];
            foreach ($roles as $role) {
                $definition = $this->roleDefinitions()[$role] ?? null;
                $targetId = (int) ($resolved[$role] ?? 0);
                if (is_array($definition) && $targetId > 0 && IPS_VariableExists($targetId)) {
                    $availableRoles[$role] = $definition;
                }
            }

            if ($availableRoles === []) {
                continue;
            }

            $groupId = $this->ensureGroup($groupKey, $group);
            if ($groupId <= 0) {
                continue;
            }

            $position = 10;
            foreach ($availableRoles as $role => $definition) {
                $targetId = (int) $resolved[$role];
                $this->ensureLink(
                    $groupId,
                    $groupKey,
                    $role,
                    $targetId,
                    $position,
                    $managedTargets
                );
                $position += 10;
            }
        }

        $this->WriteAttributeString(
            'ManagedLinkTargets',
            json_encode($managedTargets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private function vehicleOverviewRoles(): array
    {
        return [
            'vehicleName',
            'licensePlate',
            'soc',
            'range',
            'mileage',
            'locked',
            'parkingState',
            'charging',
            'chargePower',
            'targetSoc',
            'climate',
            'targetTemperature',
            'lastUpdate'
        ];
    }

    private function rolesForGroup(string $groupKey): array
    {
        $roles = [];
        foreach ($this->roleDefinitions() as $role => $definition) {
            if (($definition['group'] ?? '') === $groupKey) {
                $roles[] = $role;
            }
        }
        return $roles;
    }

    private function ensureGroup(string $groupKey, array $group): int
    {
        $ident = 'EVTILE_Group_' . ucfirst($groupKey);
        $existingId = $this->findChildByIdent($this->InstanceID, $ident);
        if ($existingId > 0) {
            $object = IPS_GetObject($existingId);
            if ((int) ($object['ObjectType'] ?? -1) !== OBJECTTYPE_INSTANCE) {
                $this->LogMessage('EV Tile: Ident ' . $ident . ' is already used by another object.', KL_WARNING);
                return 0;
            }
            return $existingId;
        }

        $id = IPS_CreateInstance(self::DUMMY_MODULE_ID);
        IPS_SetParent($id, $this->InstanceID);
        IPS_SetIdent($id, $ident);
        IPS_SetName($id, $this->Translate((string) $group['label']));
        IPS_SetIcon($id, (string) $group['icon']);
        IPS_SetPosition($id, (int) $group['position']);
        return $id;
    }

    private function ensureLink(
        int $groupId,
        string $groupKey,
        string $role,
        int $targetId,
        int $position,
        array &$managedTargets
    ): void {
        $definition = $this->roleDefinitions()[$role] ?? null;
        if (!is_array($definition)) {
            return;
        }

        $ident = 'EVTILE_Link_' . ucfirst($groupKey) . '_' . (string) $definition['ident'];
        $existingId = $this->findChildByIdent($groupId, $ident);

        if ($existingId > 0) {
            $object = IPS_GetObject($existingId);
            if ((int) ($object['ObjectType'] ?? -1) !== OBJECTTYPE_LINK) {
                $this->LogMessage('EV Tile: Ident ' . $ident . ' is already used by another object.', KL_WARNING);
                return;
            }

            $link = IPS_GetLink($existingId);
            $currentTarget = (int) ($link['TargetID'] ?? 0);
            $previousManagedTarget = (int) ($managedTargets[$ident] ?? 0);

            if ($previousManagedTarget > 0 && $currentTarget === $previousManagedTarget && $currentTarget !== $targetId) {
                IPS_SetLinkTargetID($existingId, $targetId);
                $managedTargets[$ident] = $targetId;
            } elseif ($previousManagedTarget === 0) {
                $managedTargets[$ident] = $currentTarget;
            }
            return;
        }

        $linkId = IPS_CreateLink();
        IPS_SetParent($linkId, $groupId);
        IPS_SetIdent($linkId, $ident);
        IPS_SetPosition($linkId, $position);
        IPS_SetLinkTargetID($linkId, $targetId);
        $managedTargets[$ident] = $targetId;
    }

    private function readManagedLinkTargets(): array
    {
        $decoded = json_decode($this->ReadAttributeString('ManagedLinkTargets'), true);
        if (!is_array($decoded)) {
            return [];
        }

        $result = [];
        foreach ($decoded as $ident => $targetId) {
            $result[(string) $ident] = (int) $targetId;
        }
        return $result;
    }

    private function findChildByIdent(int $parentId, string $ident): int
    {
        foreach (IPS_GetChildrenIDs($parentId) as $childId) {
            $object = IPS_GetObject($childId);
            if ((string) ($object['ObjectIdent'] ?? '') === $ident) {
                return (int) $childId;
            }
        }
        return 0;
    }
}
