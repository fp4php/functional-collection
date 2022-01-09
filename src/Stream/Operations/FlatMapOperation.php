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
class FlatMapOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-template TVO
     * @psalm-param callable(TV, TK): iterable<TVO> $f
     * @return Generator<int, TVO>
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
