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
        $this->assertEquals([1, 2, 3], $seq->toArray());
        $this->assertEquals([1, 2, 3], $seq->toLinkedList()->toArray());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyLinkedList()->toArray());
        $this->assertEquals([1, 2, 3], $seq->toArrayList()->toArray());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyArrayList()->toArray());
        $this->assertEquals([1, 2, 3], $seq->toHashSet()->toArray());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyHashSet()->toArray());
        $this->assertEquals([[1, 1], [2, 2], [3, 3]], $seq->toHashMap(fn($e) => [$e, $e])->toArray());
        $this->assertEquals([[1, 1], [2, 2], [3, 3]], $seq->toNonEmptyHashMap(fn($e) => [$e, $e])->toArray());
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
