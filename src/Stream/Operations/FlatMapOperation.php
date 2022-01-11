<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class FlatMapOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-template TValueIn
     * @psalm-param callable(TValue, TKey): iterable<TValueIn> $f
     * @return Generator<int, TValueIn>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $key => $value) {
                $xs = $f($value, $key);

                foreach ($xs as $x) {
                    yield $x;
                }
            }
        })();
    }
}
