<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @extends EmptyCollection<array{TK, TV}>
 * @extends MapOps<TK, TV>
 * @extends MapCollector<TK, TV>
 */
interface Map extends EmptyCollection, MapOps, MapCollector
{
    /**
     * @inheritDoc
     * @return Iterator<array{TK, TV}>
     */
    public function getIterator(): Iterator;
}
