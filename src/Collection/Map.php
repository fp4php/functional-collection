<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @template TKey
 * @template-covariant TValue
 * @psalm-immutable
 * @extends EmptyCollection<array{TKey, TValue}>
 * @extends MapOps<TKey, TValue>
 * @extends MapCollector<TKey, TValue>
 */
interface Map extends EmptyCollection, MapOps, MapCollector
{
    /**
     * @inheritDoc
     * @return Iterator<array{TKey, TValue}>
     */
    public function getIterator(): Iterator;
}
