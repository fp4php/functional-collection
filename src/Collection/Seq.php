<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * Ordered list of elements
 *
 * @psalm-immutable
 * @template-covariant TValue
 * @extends Collection<TValue>
 * @extends SeqOps<TValue>
 * @extends SeqCollector<TValue>
 */
interface Seq extends Collection, SeqOps, SeqCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TValue>
     */
    public function getIterator(): Iterator;
}
