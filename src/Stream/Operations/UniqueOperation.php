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
class UniqueOperation extends AbstractStreamOperation
{
    /**
     * @psalm-pure
     * @param callable(TValue): (int|string) $f
     * @return Generator<TValue>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            $hashTable = [];

            foreach ($this->gen as $value) {
                $disc = $f($value);

                if (!array_key_exists($disc, $hashTable)) {
                    $hashTable[$disc] = true;
                    yield $value;
                }
            }
        })();
    }
}
