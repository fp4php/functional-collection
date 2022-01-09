<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\NonEmptyMap;

use Whsv26\Functional\Collection\Immutable\NonEmptyMap\NonEmptyHashMap;
use Whsv26\Functional\Core\Option;
use PHPUnit\Framework\TestCase;
use Whsv26\Functional\Collection\Tests\Mock\Foo;

final class NonEmptyMapOpsTest extends TestCase
{
    public function testGet(): void
    {
        $hm = NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]]);

        $this->assertEquals(2, $hm->get('b')->get());
        $this->assertEquals(2, $hm('b')->get());
    }

    public function testUpdatedAndRemoved(): void
    {
        $hm = NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]]);
        $hm = $hm->updated('c', 3);
        $hm = $hm->removed('a');

        $this->assertEquals([['b', 2], ['c', 3]], $hm->toList());
    }

    public function testEvery(): void
    {
        $hm = NonEmptyHashMap::collectPairsUnsafe([['a', 0], ['b', 1]]);

        $this->assertTrue($hm->every(fn($entry) => $entry->value >= 0));
        $this->assertFalse($hm->every(fn($entry) => $entry->value > 0));
        $this->assertTrue($hm->every(fn($entry) => in_array($entry->key, ['a', 'b'])));
    }

    public function testEveryMap(): void
    {
        $hm = NonEmptyHashMap::collectPairsUnsafe([
            ['a', new Foo(1)],
            ['b', new Foo(2)],
        ]);

        $this->assertEquals(
            Option::some($hm),
            $hm->everyMap(fn($x) => $x->value->a >= 1 ? Option::some($x->value) : Option::none())
        );
        $this->assertEquals(
            Option::none(),
            $hm->everyMap(fn($x) => $x->value->a >= 2 ? Option::some($x->value) : Option::none())
        );
    }

    public function testFilter(): void
    {
        $hm = NonEmptyHashMap::collectPairsUnsafe([['a', new Foo(1)], ['b', 1], ['c',  new Foo(2)]]);
        $this->assertEquals([['b', 1]], $hm->filter(fn($e) => $e->value === 1)->toList());
    }

    public function testFilterMap(): void
    {
        $this->assertEquals(
            [['b', 1], ['c', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 'zero'], ['b', '1'], ['c', '2']])
                ->filterMap(fn($e) => is_numeric($e->value) ? Option::some((int) $e->value) : Option::none())
                ->toList()
        );
    }

    public function testMap(): void
    {
        $hm = NonEmptyHashMap::collectPairsNonEmpty([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [['2', '2'], ['3', '3']],
            $hm->map(fn($e) => $e->key)->toNonEmptyList()
        );

        $this->assertEquals(
            [['2', '2'], ['3', '3']],
            $hm->mapValues(fn($e) => $e->key)->toNonEmptyList()
        );

        $this->assertEquals(
            [[22, 22], [33, 33]],
            $hm->mapKeys(fn($e) => $e->value)->toNonEmptyList()
        );
    }

    public function testKeys(): void
    {
        $hm = NonEmptyHashMap::collectPairsNonEmpty([['a', 22], ['b', 33]]);

        $this->assertEquals(
            ['a', 'b'],
            $hm->keys()->toNonEmptyList()
        );
    }

    public function testValues(): void
    {
        $hm = NonEmptyHashMap::collectPairsNonEmpty([['a', 22], ['b', 33]]);

        $this->assertEquals(
            [22, 33],
            $hm->values()->toNonEmptyList()
        );
    }
}
