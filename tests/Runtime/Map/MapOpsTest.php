<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Map;

use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use PHPUnit\Framework\TestCase;

final class MapOpsTest extends TestCase
{
    public function testGet(): void
    {
        $hm = HashMap::collect(['a' => 1, 'b' => 2]);

        $this->assertEquals(2, $hm->get('b')->get());
        $this->assertEquals(2, $hm('b')->get());
    }

    public function testUpdatedAndRemoved(): void
    {
        $hm = HashMap::collect(['a' => 1, 'b' => 2]);
        $hm = $hm->updated('c', 3);
        $hm = $hm->removed('a');

        $this->assertEquals([['b', 2], ['c', 3]], $hm->stream()->compile()->toList());
    }

    public function testKeys(): void
    {
        $hm = HashMap::collectPairs([['a', 22], ['b', 33]]);

        $this->assertEquals(
            ['a', 'b'],
            $hm->keys()->toList()
        );
    }

    public function testValues(): void
    {
        $hm = HashMap::collectPairs([['a', 22], ['b', 33]]);

        $this->assertEquals(
            [22, 33],
            $hm->values()->toList()
        );
    }
}
