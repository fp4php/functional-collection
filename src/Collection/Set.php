<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends Collection<TValue>
 * @extends SetOps<TValue>
 * @extends SetCollector<TValue>
 */
interface Set extends Collection, SetOps, SetCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TValue>
     */
    public function getIterator(): Iterator;
}
