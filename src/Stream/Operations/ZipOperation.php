<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Iterator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class ZipOperation extends AbstractOperation
{
    /**
     * @template TVI
     * @param iterable<TVI> $that
     * @return Generator<int, array{TV, TVI}>
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
