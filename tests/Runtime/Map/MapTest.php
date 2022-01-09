<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Map;

use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use PHPUnit\Framework\TestCase;
use Whsv26\Functional\Collection\Tests\Mock\Bar;
use Whsv26\Functional\Collection\Tests\Mock\Foo;

final class MapTest extends TestCase
{
    public function testCasts(): void
    {
        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collectPairs([['a', 1], ['b', 2]])->toArray(),
        );

        $this->assertEquals(
            [],
            HashMap::collectPairs([])->toAssocArray(),
        );

        $this->assertEquals(
            ['a' => 1, 'b' => 2],
            HashMap::collectPairs([['a', 1], ['b', 2]])->toAssocArray(),
        );

        $this->expectExceptionMessage('Illegal offset type');
        HashMap::collectPairs([['a', 1], [new Foo(1), 2]])->toAssocArray();

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collectPairs([['a', 1], ['b', 2]])->toLinkedList()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collectPairs([['a', 1], ['b', 2]])->toArrayList()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collectPairs([['a', 1], ['b', 2]])->toHashSet()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collectPairs([['a', 1], ['b', 2]])->toHashMap()->toArray(),
        );
    }

    public function testContract(): void
    {
        $bar = new Bar(1);
        $hm = HashMap::collectPairs([[$bar, 'v1'], [new Foo(2), 'v2']]);
        $hm1 = HashMap::collectPairs([[[new Foo(1), new Foo(2)], 'v1']]);

        $this->assertEquals(
            'v2',
            $hm(new Foo(2))->get(),
        );

        $this->assertEquals(
            'v1',
            $hm1([new Foo(1), new Foo(2)])->get(),
        );

        $this->assertEquals(
            'v1',
            $hm($bar)->get(),
        );

        $this->assertNull($hm(new Bar(1))->get());
    }

    public function testCollisions(): void
    {
        $hm = HashMap::collectPairs([[1, 'v1'], [true, 'v2'], ['1', 'v3']]);
        $hm1 = HashMap::collectPairs([[0, 'v1'], [false, 'v2'], ['', 'v3']]);

        $this->assertEquals('v1', $hm(1)->get());
        $this->assertEquals('v2', $hm(true)->get());
        $this->assertEquals('v3', $hm('1')->get());

        $this->assertEquals('v1', $hm1(0)->get());
        $this->assertEquals('v2', $hm1(false)->get());
        $this->assertEquals('v3', $hm1('')->get());
    }

    public function testCount(): void
    {
        $this->assertEquals(2, HashMap::collectPairs([[1, 1], [2, 2]])->count());
    }
}
