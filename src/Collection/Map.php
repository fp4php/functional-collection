<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;
use Whsv26\Functional\Stream\Stream;

/**
 * @template TKey
 * @template-covariant TValue
 * @psalm-immutable
 * @extends Collection<array{TKey, TValue}>
 * @extends MapOps<TKey, TValue>
 * @extends MapCollector<TKey, TValue>
 */
interface Map extends Collection, MapOps, MapCollector
{
    /**
     * @inheritDoc
     * @return Iterator<array{TKey, TValue}>
     */
    public function getIterator(): Iterator;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->stream()->compile()->toList();
     * => [['a', 1], ['b', 2]]
     * ```
     *
     * @return Stream<array{TKey, TValue}>
     */
    public function stream(): Stream;
}
