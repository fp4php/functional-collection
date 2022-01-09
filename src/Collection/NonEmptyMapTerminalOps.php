<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Collection\Immutable\Map\Entry;

/**
 * @template TKey
 * @template-covariant TValue
 * @psalm-immutable
 */
interface NonEmptyMapTerminalOps
{
    /**
     * Get an element by its key
     * Alias for @see NonEmptyMapOps::get
     *
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])('b')->getOrElse(0);
     * => 2
     *
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])('c')->getOrElse(0);
     * => 0
     * ```
     *
     * @param TKey $key
     * @return Option<TValue>
     */
    public function __invoke(mixed $key): Option;

    /**
     * Get an element by its key
     *
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->get('b')->getOrElse(0);
     * => 2
     *
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->get('c')->getOrElse(0);
     * => 0
     * ```
     *
     * @param TKey $key
     * @return Option<TValue>
     */
    public function get(mixed $key): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->every(fn($entry) => $entry->value > 0);
     * => true
     *
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->every(fn($entry) => $entry->value > 1);
     * => false
     * ```
     *
     * @psalm-param callable(Entry<TKey, TValue>): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * A combined {@see NonEmptyMap::map} and {@see NonEmptyMap::every}.
     *
     * Predicate satisfying is handled via Option instead of Boolean.
     * So the output type TValueOut can be different from the input type TValue.
     *
     * ```php
     * >>> NonEmptyHashMap::collectPairs(['a' => 1, 'b' => 2])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(NonEmptyHashMap('a' -> 1, 'b' -> 2))
     *
     * >>> NonEmptyHashMap::collectPairs(['a' => 0, 'b' => 1])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @psalm-template TValueOut
     * @psalm-param callable(Entry<TKey, TValue>): Option<TValueOut> $callback
     * @psalm-return Option<NonEmptyMap<TKey, TValueOut>>
     */
    public function everyMap(callable $callback): Option;
}
