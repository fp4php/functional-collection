<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @template TKey
 * @template-covariant TValue
 * @psalm-immutable
 * @extends NonEmptyCollection<array{TKey, TValue}>
 * @extends NonEmptyMapOps<TKey, TValue>
 * @extends NonEmptyMapCollector<TKey, TValue>
 */
interface NonEmptyMap extends NonEmptyCollection, NonEmptyMapOps, NonEmptyMapCollector
{
    /**
     * @inheritDoc
     * @return Iterator<array{TKey, TValue}>
     */
    public function getIterator(): Iterator;
}
