<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends NonEmptyCollection<TValue>
 * @extends NonEmptySetOps<TValue>
 * @extends NonEmptySetCollector<TValue>
 */
interface NonEmptySet extends NonEmptyCollection, NonEmptySetOps, NonEmptySetCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TValue>
     */
    public function getIterator(): Iterator;
}
