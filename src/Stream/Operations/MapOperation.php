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
class MapOperation extends AbstractStreamOperation
{
    /**
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
