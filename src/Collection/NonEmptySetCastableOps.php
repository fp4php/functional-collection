<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Immutable\NonEmptyMap\NonEmptyHashMap;
use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyArrayList;
use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyLinkedList;
use Whsv26\Functional\Collection\Immutable\NonEmptySet\NonEmptyHashSet;
use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\Immutable\Set\HashSet;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface NonEmptySetCastableOps
{
    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->toArray();
     * => [1, 2]
     * ```
     *
     * @return non-empty-list<TValue>
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->toLinkedList();
     * => LinkedList(1, 2)
     * ```
     *
     * @return LinkedList<TValue>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->toArrayList();
     * => ArrayList(1, 2)
     * ```
     *
     * @return ArrayList<TValue>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->toNonEmptyLinkedList();
     * => NonEmptyLinkedList(1, 2)
     * ```
     *
     * @return NonEmptyLinkedList<TValue>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList;

    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->toNonEmptyArrayList();
     * => NonEmptyArrayList(1, 2)
     * ```
     *
     * @return NonEmptyArrayList<TValue>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList;

    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->toHashSet();
     * => HashSet(1, 2)
     * ```
     *
     * @return HashSet<TValue>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->toNonEmptyHashSet();
     * => NonEmptyHashSet(1, 2)
     * ```
     *
     * @return NonEmptyHashSet<TValue>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet;

    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])
     * >>>     ->toHashMap(fn($elem) => [(sting) $elem, $elem]);
     * => HashMap('1' -> 1, '2' -> 2)
     * ```
     *
     * @template TKI
     * @template TValueIn
     * @param callable(TValue): array{TKI, TValueIn} $callback
     * @return HashMap<TKI, TValueIn>
     */
    public function toHashMap(callable $callback): HashMap;

    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])
     * >>>     ->toNonEmptyHashMap(fn($elem) => [(sting) $elem, $elem]);
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     * ```
     *
     * @template TKI
     * @template TValueIn
     * @param callable(TValue): array{TKI, TValueIn} $callback
     * @return NonEmptyHashMap<TKI, TValueIn>
     */
    public function toNonEmptyHashMap(callable $callback): NonEmptyHashMap;
}
