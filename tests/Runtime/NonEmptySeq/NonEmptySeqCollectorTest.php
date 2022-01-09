<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\NonEmptySeq;

use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyArrayList;
use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyLinkedList;
use Whsv26\Functional\Core\Option;
use PHPUnit\Framework\TestCase;

final class NonEmptySeqCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyArrayList::collect([1, 2, 3])->getUnsafe()->toArray());
        $this->assertEquals([1, 2, 3], NonEmptyLinkedList::collect([1, 2, 3])->getUnsafe()->toArray());

        $this->assertTrue(Option::try(fn() => NonEmptyArrayList::collectUnsafe([]))->isNone());
        $this->assertTrue(Option::try(fn() => NonEmptyLinkedList::collectUnsafe([]))->isNone());
    }

    public function testCollectUnsafe(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyArrayList::collectUnsafe([1, 2, 3])->toArray());
        $this->assertEquals([1, 2, 3], NonEmptyLinkedList::collectUnsafe([1, 2, 3])->toArray());

        $this->assertTrue(Option::try(fn() => NonEmptyArrayList::collectUnsafe([]))->isNone());
        $this->assertTrue(Option::try(fn() => NonEmptyLinkedList::collectUnsafe([]))->isNone());
    }

    public function testCollectNonEmpty(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyArrayList::collectNonEmpty([1, 2, 3])->toArray());
        $this->assertEquals([1, 2, 3], NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toArray());
    }

    public function testCollectOption(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyArrayList::collect([1, 2, 3])->getUnsafe()->toArray());
        $this->assertEquals([1, 2, 3], NonEmptyLinkedList::collect([1, 2, 3])->getUnsafe()->toArray());

        $this->assertNull(NonEmptyArrayList::collect([])->get());
        $this->assertNull(NonEmptyLinkedList::collect([])->get());
    }
}
