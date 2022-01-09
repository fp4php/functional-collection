<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends EmptyCollection<TValue>
 * @extends SetOps<TValue>
 * @extends SetCollector<TValue>
 */
interface Set extends EmptyCollection, SetOps, SetCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TValue>
     */
    public function getIterator(): Iterator;
}
