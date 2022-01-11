<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class TapOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @param callable(TValue): void $f
     * @return Generator<TValue>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $value) {
                $f($value);
                yield $value;
            }
        })();
    }
}
