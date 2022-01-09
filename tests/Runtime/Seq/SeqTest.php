<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Seq;

use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\Seq;
use Generator;
use PHPUnit\Framework\TestCase;

final class SeqTest extends TestCase
{
    public function provideTestCastsData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([1, 2, 3]), ArrayList::collect([])];
        yield LinkedList::class => [LinkedList::collect([1, 2, 3]), LinkedList::collect([])];
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCasts(Seq $seq, Seq $emptySeq): void
    {
        $this->assertEquals([1, 2, 3], $seq->toList());
        $this->assertEquals([1, 2, 3], $seq->toLinkedList()->toList());
        $this->assertEquals([1, 2, 3], $seq->toLinkedList()->toList());
        $this->assertEquals([1, 2, 3], $seq->toArrayList()->toList());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyArrayList()->getUnsafe()->tononEmptyList());
        $this->assertNull($emptySeq->toNonEmptyArrayList()->get());
        $this->assertEquals([1, 2, 3], $seq->toArrayList()->toList());
        $this->assertEquals([1, 2, 3], $seq->toHashSet()->toList());
        $this->assertEquals([[1, 1], [2, 2], [3, 3]], $seq->toHashMap(fn($e) => [$e, $e])->toList());
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCount(Seq $seq): void
    {
        $this->assertEquals(3, $seq->count());
        $this->assertEquals(3, $seq->count());
    }
}
