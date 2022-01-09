<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends NonEmptyCollection<TValue>
 * @extends NonEmptySeqOps<TValue>
 * @extends NonEmptySeqCollector<TValue>
 */
interface NonEmptySeq extends NonEmptyCollection, NonEmptySeqOps, NonEmptySeqCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TValue>
     */
    public function getIterator(): Iterator;
}
