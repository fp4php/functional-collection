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
     * >>> HashSet::collect([1, 2])->toList();
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
     * >>> HashSet::singleton(1)->toList();
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
     * >>> HashSet::empty()->toList();
     * => []
     * ```
     *
     * @return self<empty>
     */
    public static function empty(): self;
}
