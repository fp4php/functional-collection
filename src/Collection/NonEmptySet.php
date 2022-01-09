<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptyCollection<TV>
 * @extends NonEmptySetOps<TV>
 * @extends NonEmptySetCollector<TV>
 */
interface NonEmptySet extends NonEmptyCollection, NonEmptySetOps, NonEmptySetCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;
}
