<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * Ordered list of elements
 *
 * @psalm-immutable
 * @template-covariant TV
 * @extends EmptyCollection<TV>
 * @extends SeqOps<TV>
 * @extends SeqCollector<TV>
 */
interface Seq extends EmptyCollection, SeqOps, SeqCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;
}
