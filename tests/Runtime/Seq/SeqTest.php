<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Seq;

use Generator;
use PHPUnit\Framework\TestCase;
use Whsv26\Functional\Collection\Seq;
use Whsv26\Functional\Collection\Seq\ArrayList;
use Whsv26\Functional\Collection\Seq\LinkedList;

final class SeqTest extends TestCase
{
    public function provideSeqOfNaturalNumbers(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([1, 2, 3]), ArrayList::collect([])];
        yield LinkedList::class => [LinkedList::collect([1, 2, 3]), LinkedList::collect([])];
    }

    /**
     * @dataProvider provideSeqOfNaturalNumbers
     * @param Seq<int> $seq
     */
    public function testTraverse(Seq $seq): void
    {
        $num = 0;

        foreach ($seq as $key => $value) {
            $this->assertEquals($num, $key);
            $this->assertEquals($num + 1, $value);
            $num++;
        }
    }

    /**
     * @dataProvider provideSeqOfNaturalNumbers
     */
    public function testCasts(Seq $seq): void
    {
        $this->assertEquals([1, 2, 3], $seq->toList());
    }

    /**
     * @dataProvider provideSeqOfNaturalNumbers
     */
    public function testCount(Seq $seq): void
    {
        $this->assertEquals(3, $seq->count());
        $this->assertEquals(3, $seq->count());
    }
}
