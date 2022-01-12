<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Map;

use PHPUnit\Framework\TestCase;
use Whsv26\Functional\Collection\HashMap;

final class MapCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collectPairs([['a', 1], ['b', 2]])->stream()->compile()->toList(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collect(['a' => 1, 'b' => 2])->stream()->compile()->toList(),
        );
    }
}
