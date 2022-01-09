<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\NonEmptySet;

use Whsv26\Functional\Collection\Immutable\NonEmptySet\NonEmptyHashSet;
use Whsv26\Functional\Collection\NonEmptySet;
use Generator;
use PHPUnit\Framework\TestCase;

final class NonEmptySetTest extends TestCase
{
    public function provideTestCastsData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, 2, 3, 3])];
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCasts(NonEmptySet $set): void
    {
        $this->assertEquals([1, 2, 3], $set->toNonEmptyList());
        $this->assertEquals([1, 2, 3], $set->toLinkedList()->toList());
        $this->assertEquals([1, 2, 3], $set->toNonEmptyLinkedList()->toNonEmptyList());
        $this->assertEquals([1, 2, 3], $set->toArrayList()->toList());
        $this->assertEquals([1, 2, 3], $set->toNonEmptyArrayList()->toNonEmptyList());
        $this->assertEquals([1, 2, 3], $set->toHashSet()->toList());
        $this->assertEquals([1, 2, 3], $set->toNonEmptyHashSet()->toNonEmptyList());
        $this->assertEquals([[1, 1], [2, 2], [3, 3]], $set->toHashMap(fn($e) => [$e, $e])->toList());
        $this->assertEquals([[1, 1], [2, 2], [3, 3]], $set->toNonEmptyHashMap(fn($e) => [$e, $e])->toNonEmptyList());
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCount(NonEmptySet $set): void
    {
        $this->assertEquals(3, $set->count());
    }
}
