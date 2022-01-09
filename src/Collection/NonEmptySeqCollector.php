<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface NonEmptySeqCollector
{
    /**
     * ```php
     * >>> NonEmptyArrayList::collect([1, 2]);
     * => Some(NonEmptyArrayList(1, 2))
     *
     * >>> NonEmptyArrayList::collect([]);
     * => None
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $source
     * @return Option<self<TValueIn>>
     */
    public static function collect(iterable $source): Option;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectUnsafe([1, 2]);
     * => NonEmptyArrayList(1, 2)
     *
     * >>> NonEmptyArrayList::collectUnsafe([]);
     * PHP Error: Trying to get value of None
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $source
     * @return self<TValueIn>
     */
    public static function collectUnsafe(iterable $source): self;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2]);
     * => NonEmptyArrayList(1, 2)
     * ```
     *
     * @template TValueIn
     * @param non-empty-array<TValueIn>|NonEmptyCollection<TValueIn> $source
     * @return self<TValueIn>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): self;
}
