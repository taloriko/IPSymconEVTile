<?php

declare(strict_types=1);

require_once __DIR__ . '/src/CoreTrait.php';
require_once __DIR__ . '/src/MappingTrait.php';
require_once __DIR__ . '/src/ObjectTreeTrait.php';
require_once __DIR__ . '/src/ChartTrait.php';

final class EVTile extends IPSModuleStrict
{
    use EVTileCoreTrait;
    use EVTileMappingTrait;
    use EVTileObjectTreeTrait;
    use EVTileChartTrait;
}
