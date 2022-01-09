<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Map;

use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use PHPUnit\Framework\TestCase;

final class MapCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collectPairs([['a', 1], ['b', 2]])->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collect(['a' => 1, 'b' => 2])->toArray(),
        );
    }
}
