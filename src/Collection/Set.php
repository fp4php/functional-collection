<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;
use Whsv26\Functional\Stream\Stream;

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

    /**
     * ```php
     * >>> HashSet::collect([1, 2])->stream()->compile()->toList();
     * => [1, 2]
     * ```
     *
     * @return Stream<TValue>
     */
    public function stream(): Stream;
}
