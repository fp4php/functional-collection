<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class MapKeysOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TKeyOut
     * @param callable(TValue): TKeyOut $f
     * @return Generator<TKeyOut, TValue>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $value) {
                yield $f($value) => $value;
            }
        })();
    }
}
