<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends EmptyCollection<TV>
 * @extends SetOps<TV>
 * @extends SetCollector<TV>
 */
interface Set extends EmptyCollection, SetOps, SetCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;
}
