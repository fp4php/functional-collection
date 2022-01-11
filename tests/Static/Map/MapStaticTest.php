<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Static\Map;

use Whsv26\Functional\Collection\Map;

final class MapStaticTest
{
    /**
     * @param Map<string, int> $coll
     * @return array<string, int>
     */
    public function testToAssocArrayWithValidInput(Map $coll): array
    {
        return $coll->toArray();
    }
}
