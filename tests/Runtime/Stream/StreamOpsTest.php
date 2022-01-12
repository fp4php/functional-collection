<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Stream;

use Whsv26\Functional\Collection\Seq;
use Whsv26\Functional\Stream\Stream;
use Whsv26\Functional\Core\Option;
use PHPUnit\Framework\TestCase;
use Whsv26\Functional\Collection\Tests\Mock\Bar;
use Whsv26\Functional\Collection\Tests\Mock\Foo;

final class StreamOpsTest extends TestCase
{
    public function testEvery(): void
    {
        $every = Stream::emits([0, 1, 2, 3, 4, 5])->compile();
        $some = Stream::emits([0, 1, 2, 3, 4, 5])->compile();

        $this->assertTrue($every->every(fn($i) => $i >= 0));
        $this->assertFalse($some->every(fn($i) => $i > 0));
    }

    public function testEveryOf(): void
    {
        $this->assertTrue(Stream::emits([new Foo(1), new Foo(2)])->compile()->everyOf(Foo::class));
        $this->assertFalse(Stream::emits([new Foo(1), new Bar(2)])->compile()->everyOf(Foo::class));
    }

    public function testExists(): void
    {
        /** @psalm-var Stream<object|scalar> $hasOne */
        $hasOne = Stream::emits([new Foo(1), 1, 1, new Foo(1)]);

        /** @psalm-var Stream<object|scalar> $hasNotTwo */
        $hasNotTwo = Stream::emits([new Foo(1), 1, 1, new Foo(1)]);

        $this->assertTrue($hasOne->compile()->exists(fn($i) => $i === 1));
        $this->assertFalse($hasNotTwo->compile()->exists(fn($i) => $i === 2));
    }

    public function testExistsOf(): void
    {
        $hasFoo = Stream::emits([new Foo(1), 1, 1, new Foo(1)])->compile();
        $hasNotFoo = Stream::emits([new Foo(1), 1, 1, new Foo(1)])->compile();

        $this->assertTrue($hasFoo->existsOf(Foo::class));
        $this->assertFalse($hasNotFoo->existsOf(Bar::class));
    }

    public function testFilter(): void
    {
        $hs = Stream::emits([new Foo(1), 1, new Foo(1)]);

        $this->assertEquals([1], $hs->filter(fn($i) => $i === 1)->compile()->toList());
        $this->assertEquals([1], Stream::emits([1, null])->filterNotNull()->compile()->toList());
    }

    public function testFilterOf(): void
    {
        $hs = Stream::emits([new Foo(1), 1, 2, new Foo(1)]);
        $this->assertCount(2, $hs->filterOf(Foo::class)->compile()->toList());
    }

    public function testFilterMap(): void
    {
        $this->assertEquals(
            [1, 2],
            Stream::emits(['zero', '1', '2'])
                ->filterMap(fn($e) => is_numeric($e) ? Option::some((int) $e) : Option::none())
                ->compile()
                ->toList()
        );
    }

    public function testFirstsAndLasts(): void
    {
        $this->assertEquals('1', Stream::emits(['1', 2, '3'])->compile()->first(fn($i) => is_string($i))->get());
        $this->assertEquals('1', Stream::emits(['1', 2, '3'])->compile()->firstElement()->get());

        $this->assertEquals('3', Stream::emits(['1', 2, '3'])->compile()->last(fn($i) => is_string($i))->get());
        $this->assertEquals('3', Stream::emits(['1', 2, '3'])->compile()->lastElement()->get());

        $s = Stream::emits([$f1 = new Foo(1), 2, new Foo(2)]);
        $this->assertEquals($f1, $s->compile()->firstOf(Foo::class)->get());
    }

    public function testFlatMap(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            Stream::emits([2, 5])
                ->flatMap(fn($e) => [$e - 1, $e, $e + 1])
                ->compile()
                ->toList()
        );
    }

    public function testFold(): void
    {
        /** @psalm-var Stream<int> $list */
        $list = Stream::emits([2, 3]);

        $this->assertEquals(
            6,
            $list->compile()->fold(1, fn(int $acc, $e) => $acc + $e)
        );
    }

    public function testReduce(): void
    {
        /** @psalm-var Stream<string> $list */
        $list = Stream::emits(['1', '2', '3']);

        $this->assertEquals(
            '123',
            $list->compile()->reduce(fn(string $acc, $e) => $acc . $e)->get()
        );
    }

    public function testMap(): void
    {
        $this->assertEquals(
            ['2', '3', '4'],
            Stream::emits([1, 2, 3])->map(fn($e) => (string) ($e + 1))->compile()->toList()
        );

        $this->assertEquals(
            ['_b'],
            Stream::emits([['a', 0], ['b', 1]])
                ->filterKeys(fn($key) => $key !== 'a')
                ->mapKeys(fn($key) => '_' . $key)
                ->keys()
                ->compile()
                ->toList()
        );

        $this->assertEquals(
            [2],
            Stream::emits([['a', 0], ['b', 1]])
                ->mapValues(fn($val) => $val + 1)
                ->filterValues(fn($val) => $val > 1)
                ->values()
                ->compile()
                ->toList()
        );
    }

    public function testTap(): void
    {
        $this->assertEquals(
            [2, 3],
            Stream::emits([new Foo(1), new Foo(2)])
                ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
                ->map(fn(Foo $foo) => $foo->a)
                ->compile()
                ->toList()
        );
    }

    public function testRepeat(): void
    {
        $this->assertEquals([1, 2, 1, 2, 1], Stream::emits([1,2])->repeat()->take(5)->compile()->toList());
        $this->assertEquals([1], Stream::emit(1)->repeatN(1)->compile()->toList());
        $this->assertEquals([1, 1, 1], Stream::emit(1)->repeatN(3)->compile()->toList());
    }

    public function testAppendedAndPrepended(): void
    {
        $this->assertEquals(
            [-2, -1, 0, 1, 2, 3, 4, 5, 6],
            Stream::emits([1, 2, 3])
                ->prepended(0)
                ->appended(4)
                ->appendedAll([5, 6])
                ->prependedAll([-2, -1])
                ->compile()
                ->toList()
        );
    }

    public function testTail(): void
    {
        $this->assertEquals(
            [2, 3],
            Stream::emits([1, 2, 3])->tail()->compile()->toList()
        );
    }

    public function testTakeAndDrop(): void
    {
        $this->assertEquals([0, 1], Stream::emits([0, 1, 2])->takeWhile(fn($e) => $e < 2)->compile()->toList());
        $this->assertEquals([2], Stream::emits([0, 1, 2])->dropWhile(fn($e) => $e < 2)->compile()->toList());
        $this->assertEquals([0, 1], Stream::emits([0, 1, 2])->take(2)->compile()->toList());
        $this->assertEquals([2], Stream::emits([0, 1, 2])->drop(2)->compile()->toList());
    }

    public function testIntersperse(): void
    {
        $this->assertEquals([0, '.', 1, '.', 2], Stream::emits([0, 1, 2])->intersperse('.')->compile()->toList());
        $this->assertEquals([], Stream::emits([])->intersperse('.')->compile()->toList());
    }

    public function testLines(): void
    {
        Stream::emits([1, 2])->lines()->compile()->drain();
        $this->expectOutputString('12');
    }

    public function testInterleave(): void
    {
        $this->assertEquals(
            [0, 'a', 1, 'b'],
            Stream::emits([0, 1, 2])->interleave(['a', 'b'])->compile()->toList()
        );

        $this->assertEquals(
            [0, 'a', 1, 'b'],
            Stream::emits([0, 1])->interleave(['a', 'b', 'c'])->compile()->toList()
        );
    }

    public function testZip(): void
    {
        $this->assertEquals(
            [[0, 'a'], [1, 'b']],
            Stream::emits([0, 1, 2])->zip(['a', 'b'])->compile()->toList()
        );

        $this->assertEquals(
            [[0, 'a'], [1, 'b']],
            Stream::emits([0, 1])->zip(['a', 'b', 'c'])->compile()->toList()
        );
    }

    public function testChunks(): void
    {
        $this->assertEquals(
            [[1, 2], [3, 4], [5]],
            Stream::emits([1, 2, 3, 4, 5])
                ->chunks(2)
                ->map(fn(Seq $seq) => $seq->toList())
                ->compile()
                ->toList()
        );
    }

    public function testGroupAdjacentBy(): void
    {
        $this->assertEquals(
            [["H", ["Hello", "Hi"]], ["G", ["Greetings"]], ["H", ["Hey"]]],
            Stream::emits(["Hello", "Hi", "Greetings", "Hey"])
                ->groupAdjacentBy(fn($str) => $str[0])
                ->map(fn($pair) => [$pair[0], $pair[1]->toList()])
                ->compile()
                ->toList()
        );
    }

    public function testHead(): void
    {
        $this->assertEquals(
            1,
            Stream::emits([1, 2, 3])->compile()->head()->get()
        );
    }

    public function testMkString(): void
    {
        $this->assertEquals('(0,1,2)', Stream::emits([0, 1, 2])->compile()->mkString('(', ',', ')'));
        $this->assertEquals('()', Stream::emits([])->compile()->mkString('(', ',', ')'));
    }

    public function testSorted(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            Stream::emits([1, 3, 2])->sorted(fn($lhs, $rhs) => $lhs - $rhs)->compile()->toList()
        );

        $this->assertEquals(
            [3, 2, 1],
            Stream::emits([1, 3, 2])->sorted(fn($lhs, $rhs) => $rhs - $lhs)->compile()->toList()
        );
    }
}
