<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface SeqCollector
{
    /**
     * ```php
     * >>> LinkedList::collect([1, 2])->toList();
     * => [1, 2]
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $source
     * @return self<TValueIn>
     */
    public static function collect(iterable $source): self;

    /**
     * ```php
     * >>> LinkedList::singleton(1)->toList();
     * => [1]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $val
     * @return self<TValueIn>
     */
    public static function singleton(mixed $val): self;

    /**
     * ```php
     * >>> LinkedList::empty()->toList();
     * => []
     * ```
     *
     * @return self<empty>
     */
    public static function empty(): self;

    /**
     * Collect elements
     * from $start to $stopExclusive with step $by.
     *
     * ```php
     * >>> LinkedList::range(0, 10, 2)->toList();
     * => [0, 2, 4, 6, 8]
     *
     * >>> LinkedList::range(0, 3)->toList();
     * => [0, 1, 2]
     *
     * >>> LinkedList::range(0, 0)->toList();
     * => []
     * ```
     *
     * @param positive-int $by
     * @return self<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): self;
}
