<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends BaseCollection<TValue>
 */
interface EmptyCollection extends BaseCollection
{
    /**
     * @inheritDoc
     * @return Iterator<TValue>
     */
    public function getIterator(): Iterator;
}
