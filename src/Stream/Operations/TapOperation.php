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
class TapOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @param callable(TV, TK): void $f
     * @return Generator<TK, TV>
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
