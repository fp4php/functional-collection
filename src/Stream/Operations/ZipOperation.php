<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class ZipOperation extends AbstractOperation
{
    /**
     * @template TValueIn
     * @param iterable<TValueIn> $that
     * @return Generator<int, array{TValue, TValueIn}>
     */
    public function __invoke(iterable $that): Generator
    {
        return (function () use ($that) {
            $thisIter = $this->gen;
            $thatIter = (function () use ($that) {
                foreach ($that as $value) {
                    yield $value;
                }
            })();

            $thisIter->rewind();
            $thatIter->rewind();

            while ($thisIter->valid() && $thatIter->valid()) {
                $thisElem = $thisIter->current();
                $thatElem = $thatIter->current();

                yield [$thisElem, $thatElem];

                $thisIter->next();
                $thatIter->next();
            }
        })();
    }
}
