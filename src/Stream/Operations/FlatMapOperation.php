<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class FlatMapOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-template TValueIn
     * @psalm-param callable(TValue): iterable<TValueIn> $f
     * @return Generator<int, TValueIn>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $value) {
                $xs = $f($value);

                foreach ($xs as $x) {
                    yield $x;
                }
            }
        })();
    }
}
