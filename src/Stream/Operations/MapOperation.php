<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class MapOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TValueIn
     * @param callable(TValue): TValueIn $f
     * @return Generator<TValueIn>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $value) {
                yield $f($value);
            }
        })();
    }
}
