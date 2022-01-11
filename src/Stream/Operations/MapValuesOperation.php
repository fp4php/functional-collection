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
class MapValuesOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TValueIn
     * @param callable(TValue, TKey): TValueIn $f
     * @return Generator<TKey, TValueIn>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $key => $value) {
                yield $key => $f($value, $key);
            }
        })();
    }
}
