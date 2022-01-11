<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Generator;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class SortedOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-param callable(TValue, TValue): int $f
     * @return Generator<TValue>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            $sorted = ArrayList::collect($this->gen)->toList();
            usort($sorted, $f);

            foreach ($sorted as $value) {
                yield $value;
            }
        })();
    }
}
