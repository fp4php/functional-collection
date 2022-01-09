<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class MapValuesOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TVO
     * @param callable(TV, TK): TVO $f
     * @return Generator<TK, TVO>
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
