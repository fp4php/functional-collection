<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends BaseCollection<TValue>
 */
interface NonEmptyCollection extends BaseCollection
{
    /**
     * @mutation-free
     * @inheritDoc
     * @return Iterator<TValue>
     */
    public function getIterator(): Iterator;
}
