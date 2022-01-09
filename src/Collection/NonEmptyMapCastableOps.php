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
 * @template TK
 * @template-covariant TV
 */
interface NonEmptyMapCastableOps
{
    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toArray();
     * => [['a', 1], ['b', 2]]
     * ```
     *
     * @return non-empty-list<array{TK, TV}>
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toLinkedList();
     * => LinkedList(['a', 1], ['b', 2])
     * ```
     *
     * @return LinkedList<array{TK, TV}>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toNonEmptyLinkedList();
     * => NonEmptyLinkedList(['a', 1], ['b', 2])
     * ```
     *
     * @return NonEmptyLinkedList<array{TK, TV}>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toArrayList();
     * => ArrayList(['a', 1], ['b', 2])
     * ```
     *
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toNonEmptyArrayList();
     * => NonEmptyArrayList(['a', 1], ['b', 2])
     * ```
     *
     * @return NonEmptyArrayList<array{TK, TV}>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toHashSet();
     * => HashSet(['a', 1], ['b', 2])
     * ```
     *
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toNonEmptyHashSet();
     * => NonEmptyHashSet(['a', 1], ['b', 2])
     * ```
     *
     * @return NonEmptyHashSet<array{TK, TV}>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toHashMap();
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toNonEmptyHashMap();
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @return NonEmptyHashMap<TK, TV>
     */
    public function toNonEmptyHashMap(): NonEmptyHashMap;
}
