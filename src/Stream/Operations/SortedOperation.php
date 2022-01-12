<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Whsv26\Functional\Collection\ArrayList;
use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class SortedOperation extends AbstractStreamOperation
{
    /**
     * @param callable(TValue, TValue): int $f
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
