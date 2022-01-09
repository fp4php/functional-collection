<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Countable;
use Iterator;
use IteratorAggregate;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @implements IteratorAggregate<empty, TValue>
 */
interface BaseCollection extends IteratorAggregate, Countable
{
    /**
     * @inheritDoc
     * @return Iterator<TValue>
     */
    public function getIterator(): Iterator;
}
