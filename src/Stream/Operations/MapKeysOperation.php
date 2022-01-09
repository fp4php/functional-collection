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
class MapKeysOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TKO
     * @param callable(TV, TK): TKO $f
     * @return Generator<TKO, TV>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $key => $value) {
                yield $f($value, $key) => $value;
            }
        })();
    }
}
