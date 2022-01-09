<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\NonEmptySeq;

use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyArrayList;
use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyLinkedList;
use Whsv26\Functional\Collection\NonEmptySeq;
use Generator;
use PHPUnit\Framework\TestCase;

final class NonEmptySeqTest extends TestCase
{
    public function provideTestCastsData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([1, 2, 3])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([1, 2, 3])];
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCasts(NonEmptySeq $seq): void
    {
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyList());
        $this->assertEquals([1, 2, 3], $seq->toLinkedList()->toList());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyLinkedList()->toNonEmptyList());
        $this->assertEquals([1, 2, 3], $seq->toArrayList()->toList());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyArrayList()->toNonEmptyList());
        $this->assertEquals([1, 2, 3], $seq->toHashSet()->toList());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyHashSet()->toNonEmptyList());
        $this->assertEquals([[1, 1], [2, 2], [3, 3]], $seq->toHashMap(fn($e) => [$e, $e])->toList());
        $this->assertEquals([[1, 1], [2, 2], [3, 3]], $seq->toNonEmptyHashMap(fn($e) => [$e, $e])->toNonEmptyList());
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCount(NonEmptySeq $seq): void
    {
        $this->assertEquals(3, $seq->count());
        $this->assertEquals(3, $seq->count());
    }
}
