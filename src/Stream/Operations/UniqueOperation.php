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
class UniqueOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-param callable(TValue, TKey): (int|string) $f
     * @return Generator<TKey, TValue>
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
