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
class TapOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @param callable(TValue, TKey): void $f
     * @return Generator<TKey, TValue>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $key => $value) {
                $f($value, $key);
                yield $key => $value;
            }
        })();
    }
}
