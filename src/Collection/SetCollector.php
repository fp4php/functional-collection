<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface SetCollector
{
    /**
     * ```php
     * >>> HashSet::collect([1, 2]);
     * => HashSet(1, 2)
     * ```
     *
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self;
}
