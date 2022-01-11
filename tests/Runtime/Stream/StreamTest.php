<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Stream;

use Whsv26\Functional\Core\Option\Some;
use Whsv26\Functional\Stream\Stream;
use Whsv26\Functional\Core\Option;
use PHPUnit\Framework\TestCase;

final class StreamTest extends TestCase
{
    public function testDoubleDrain(): void
    {
        $stream = Stream::emits([0, 1])->compile();
        $stream->drain();

        $this->assertNull(Option::try(fn() => $stream->drain())->get());
    }

    public function testForkDetection(): void
    {
        $stream = Stream::emits([0, 1]);
        $fork1 = $stream->map(fn($i) => $i + 1);

        $this->assertNull(Option::try(fn() => $stream->map(fn($i) => $i + 1))->get());
    }

    public function testCasts(): void
    {
        $this->assertInstanceOf(Some::class, Option::try(fn() => Stream::emits([0, 1])
            ->compile()
            ->toFile('/dev/null', false)
        ));

        $this->assertInstanceOf(Some::class, Option::try(fn() => Stream::emits([0, 1])
            ->compile()
            ->toFile('/dev/null', true)
        ));

        $this->assertEquals([0, 1], Stream::emits([0, 1])->compile()->toList());
        $this->assertEquals([0, 1], Stream::emits([0, 1])->compile()->toNonEmptyList()->getUnsafe());
        $this->assertNull(Stream::emits([])->compile()->toNonEmptyList()->get());
        $this->assertEquals([0, 1], Stream::emits([0, 1])->compile()->toArrayList()->toList());
        $this->assertEquals([0, 1], Stream::emits([0, 1])->compile()->toLinkedList()->toList());
        $this->assertEquals([0, 1], Stream::emits([0, 1, 1])->compile()->toHashSet()->toList());

        $this->assertEquals([[0, 0], [1, 1]], Stream::emits([[0, 0], [1, 1]])
            ->compile()
            ->toHashMap()
            ->stream()
            ->compile()
            ->toList()
        );

        $this->assertEquals(
            [1 => 'a', 2 => 'b'],
            Stream::emits([[1, 'a'], [2, 'b']])->compile()->toArray()
        );
    }
}
