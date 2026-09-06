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
            $roles = [];
            foreach ($this->roleDefinitions() as $role => $definition) {
                if ($definition['group'] === $groupKey && ($resolved[$role] ?? 0) > 0) {
                    $roles[$role] = $definition;
                }
            }

            if ($roles === []) {
                continue;
            }

            $groupId = $this->ensureGroup($groupKey, $group);
            if ($groupId <= 0) {
                continue;
            }

            foreach ($roles as $role => $definition) {
                $targetId = (int) ($resolved[$role] ?? 0);
                if ($targetId > 0 && IPS_VariableExists($targetId)) {
                    $this->ensureLink($groupId, $definition, $targetId, $managedTargets);
                }
            }
        }

        $this->WriteAttributeString(
            'ManagedLinkTargets',
            json_encode($managedTargets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private function ensureGroup(string $groupKey, array $group): int
    {
        $ident = 'EVTILE_Group_' . ucfirst($groupKey);
        $existingId = $this->findChildByIdent($this->InstanceID, $ident);
        if ($existingId > 0) {
            $object = IPS_GetObject($existingId);
            if ((int) ($object['ObjectType'] ?? -1) !== 1) {
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

    private function ensureLink(int $groupId, array $definition, int $targetId, array &$managedTargets): void
    {
        $ident = 'EVTILE_Link_' . (string) $definition['ident'];
        $existingId = $this->findChildByIdent($groupId, $ident);

        if ($existingId > 0) {
            $object = IPS_GetObject($existingId);
            if ((int) ($object['ObjectType'] ?? -1) !== 6) {
                $this->LogMessage('EV Tile: Ident ' . $ident . ' is already used by another object.', KL_WARNING);
                return;
            }

            $link = IPS_GetLink($existingId);
            $currentTarget = (int) ($link['TargetID'] ?? 0);
            $previousManagedTarget = (int) ($managedTargets[$ident] ?? 0);

            // Only retarget a link if it still points to the last target managed by EV Tile.
            // A user-modified link target therefore remains user-owned.
            if ($previousManagedTarget > 0 && $currentTarget === $previousManagedTarget && $currentTarget !== $targetId) {
                IPS_SetLinkTargetID($existingId, $targetId);
                $managedTargets[$ident] = $targetId;
            } elseif ($previousManagedTarget === 0) {
                // Existing objects without management metadata are treated as user-owned.
                $managedTargets[$ident] = $currentTarget;
            }
            return;
        }

        $linkId = IPS_CreateLink();
        IPS_SetParent($linkId, $groupId);
        IPS_SetIdent($linkId, $ident);
        IPS_SetName($linkId, $this->Translate((string) $definition['label']));
        IPS_SetIcon($linkId, (string) $definition['icon']);
        IPS_SetPosition($linkId, (int) $definition['position']);
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
