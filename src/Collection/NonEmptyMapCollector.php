<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template TKey
 * @template-covariant TValue
 */
interface NonEmptyMapCollector
{
    /**
     * ```php
     * >>> NonEmptyHashMap::collect(['a' =>  1, 'b' => 2]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @param iterable<TKeyIn, TValueIn> $source
     * @return Option<self<TKeyIn, TValueIn>>
     */
    public static function collect(iterable $source): Option;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectUnsafe(['a' =>  1, 'b' => 2]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @param iterable<TKeyIn, TValueIn> $source
     * @return self<TKeyIn, TValueIn>
     */
    public static function collectUnsafe(iterable $source): self;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' =>  1, 'b' => 2]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @param non-empty-array<TKeyIn, TValueIn> $source
     * @return self<TKeyIn, TValueIn>
     */
    public static function collectNonEmpty(array $source): self;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectPairs([['a', 1], ['b', 2]]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @param iterable<array{TKeyIn, TValueIn}> $source
     * @return Option<self<TKeyIn, TValueIn>>
     */
    public static function collectPairs(iterable $source): Option;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @param iterable<array{TKeyIn, TValueIn}> $source
     * @return self<TKeyIn, TValueIn>
     */
    public static function collectPairsUnsafe(iterable $source): self;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @param non-empty-array<array{TKeyIn, TValueIn}>|NonEmptyCollection<array{TKeyIn, TValueIn}> $source
     * @return self<TKeyIn, TValueIn>
     */
    public static function collectPairsNonEmpty(array|NonEmptyCollection $source): self;
}
