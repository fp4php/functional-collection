<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Static\Stream;

use Whsv26\Functional\Collection\Tests\Mock\Foo;
use Whsv26\Functional\Stream\Stream;
use Whsv26\Functional\Stream\StreamChainableOps;

final class StreamStaticTest
{
    /**
     * @psalm-param int $input
     * @psalm-return Stream<int>
     */
    public function testEmit(mixed $input): Stream
    {
        return Stream::emit($input);
    }

    /**
     * @psalm-param array{1, 2, 'a'} $input
     * @psalm-return Stream<1|2|'a'>
     */
    public function testEmits(mixed $input): Stream
    {
        return Stream::emits($input);
    }

    /**
     * @param Stream<array{int, Foo}> $stream
     * @param StreamChainableOps<array{int, Foo}> $ops
     * @return array{Stream<array{string, Foo}>, StreamChainableOps<array{string, Foo}>}
     */
    public function testMapKeys(Stream $stream, StreamChainableOps $ops): array
    {
        return [
            $stream->mapKeys(fn(int $key) => (string) $key),
            $ops->mapKeys(fn(int $key) => (string) $key)
        ];
    }

    /**
     * @param Stream<array{string, Foo}> $input
     * @return Stream<array{string, int}>
     */
    public function testMapValues(Stream $input): Stream
    {
        return $input->mapValues(fn(Foo $val) => $val->a);
    }
}
