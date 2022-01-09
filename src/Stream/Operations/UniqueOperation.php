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
class UniqueOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-param callable(TV, TK): (int|string) $f
     * @return Generator<TK, TV>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            $hashTable = [];

            foreach ($this->gen as $key => $value) {
                $disc = $f($value, $key);

                if (!array_key_exists($disc, $hashTable)) {
                    $hashTable[$disc] = true;
                    yield $key => $value;
                }
            }
        })();
    }
}
