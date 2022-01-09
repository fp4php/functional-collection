<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\NonEmptyMap;

use Whsv26\Functional\Collection\Immutable\NonEmptyMap\NonEmptyHashMap;
use PHPUnit\Framework\TestCase;

final class NonEmptyMapTest extends TestCase
{
    public function testCasts(): void
    {
        $expected = [['a', 1], ['b', 2]];

        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyList());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toLinkedList()->toList());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyLinkedList()->toNonEmptyList());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toArrayList()->toList());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyArrayList()->toNonEmptyList());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toHashSet()->toList());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyHashSet()->toNonEmptyList());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toHashMap()->toList());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyHashMap()->toNonEmptyList());
    }

    public function testCount(): void
    {
        $this->assertCount(2, NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]]));
    }
}
