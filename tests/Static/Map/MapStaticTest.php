<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Static\Map;

use Whsv26\Functional\Collection\Map;
use Whsv26\Functional\Collection\Seq;

final class MapStaticTest
{
    public function testToAssocArrayWithInvalidInput(Map $coll): void
    {
        // TODO
    }

    /**
     * @param Map<string, int> $coll
     * @return array<string, int>
     */
    public function testToAssocArrayWithValidInput(Map $coll): array
    {
        return $coll->toAssocArray();
    }

    /**
     * @param Seq<array{string, int}> $coll
     * @return array<string, int>
     */
    public function testToAssocArrayFromSeq(Seq $coll): array
    {
        return $coll
            ->toHashMap(fn($pair) => $pair)
            ->toAssocArray();
    }
}
