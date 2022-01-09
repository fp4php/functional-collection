<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface SetCollector
{
    /**
     * ```php
     * >>> HashSet::collect([1, 2]);
     * => HashSet(1, 2)
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $source
     * @return self<TValueIn>
     */
    public static function collect(iterable $source): self;
}
