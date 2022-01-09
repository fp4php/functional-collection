<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\NonEmptyMap;

use Whsv26\Functional\Collection\Immutable\NonEmptyMap\NonEmptyHashMap;
use Whsv26\Functional\Core\Option;
use Generator;
use PHPUnit\Framework\TestCase;

final class NonEmptyMapCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertNull(NonEmptyHashMap::collect([])->get()?->toNonEmptyList());
        $this->assertEquals([['a', 1]], NonEmptyHashMap::collect(['a' => 1])->get()?->toNonEmptyList());
    }

    public function testCollectUnsafe(): void
    {
        $this->assertNull(Option::try(fn() => NonEmptyHashMap::collectUnsafe([]))->get());
    }

    public function testCollectNonEmpty(): void
    {
        $this->assertEquals(
            [['a', 1]],
            NonEmptyHashMap::collectNonEmpty(['a' => 1])->toNonEmptyList()
        );
    }

    public function provideTestCollectPairsData(): Generator
    {
        yield NonEmptyHashMap::class => [
            NonEmptyHashMap::collectPairs([['a', 1], ['b', 2]]),
            NonEmptyHashMap::collectPairs([]),
        ];
    }

    /**
     * @dataProvider provideTestCollectPairsData
     * @param Option<NonEmptyHashMap> $m1
     * @param Option<NonEmptyHashMap> $m2
     */
    public function testCollectPairs(Option $m1, Option $m2): void
    {
        $expected = [['a', 1], ['b', 2]];
        $this->assertEquals($expected, $m1->getUnsafe()->toNonEmptyList());
        $this->assertNull($m2->get());
    }

    public function provideTestCollectPairsUnsafeData(): Generator
    {
        yield NonEmptyHashMap::class => [
            fn() => NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]]),
            fn() => NonEmptyHashMap::collectPairsUnsafe([])
        ];
    }

    /**
     * @dataProvider provideTestCollectPairsUnsafeData
     * @param callable(): NonEmptyHashMap $t1
     * @param callable(): NonEmptyHashMap $t2
     */
    public function testCollectPairsUnsafe(callable $t1, callable $t2): void
    {
        $expected = [['a', 1], ['b', 2]];
        $this->assertEquals($expected, Option::try(fn() => $t1()->toNonEmptyList())->get());
        $this->assertNull(Option::try(fn() => $t2()->toNonEmptyList())->get());
    }
}
