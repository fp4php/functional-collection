<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class InterleaveOperation extends AbstractStreamOperation
{
    /**
     * @template TValueIn
     * @param iterable<TValueIn> $that
     * @return Generator<int, TValue|TValueIn>
     */
    public function __invoke(iterable $that): Generator
    {
        $pairs = ZipOperation::of($this->gen)($that);

        return FlatMapOperation::of($pairs)(function (array $pair) {
            yield from $pair;
        });
    }
}
