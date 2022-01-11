<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;
use Whsv26\Functional\Stream\Stream;

/**
 * Ordered list of elements
 *
 * @psalm-immutable
 * @template-covariant TValue
 * @extends Collection<TValue>
 * @extends SeqOps<TValue>
 * @extends SeqCollector<TValue>
 */
interface Seq extends Collection, SeqOps, SeqCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TValue>
     */
    public function getIterator(): Iterator;

    /**
     * ```php
     * >>> ArrayList::collect([1, 2])->stream()->compile()->toList();
     * => [1, 2]
     * ```
     *
     * @return Stream<TValue>
     */
    public function stream(): Stream;
}
