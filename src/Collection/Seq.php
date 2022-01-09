<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * Ordered list of elements
 *
 * @psalm-immutable
 * @template-covariant TValue
 * @extends EmptyCollection<TValue>
 * @extends SeqOps<TValue>
 * @extends SeqCollector<TValue>
 */
interface Seq extends EmptyCollection, SeqOps, SeqCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TValue>
     */
    public function getIterator(): Iterator;
}
