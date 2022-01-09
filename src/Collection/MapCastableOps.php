<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\Immutable\Set\HashSet;

/**
 * @psalm-immutable
 * @template TKey
 * @template-covariant TValue
 */
interface MapCastableOps
{
    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toArray();
     * => [['a', 1], ['b', 2]]
     * ```
     *
     * @return list<array{TKey, TValue}>
     */
    public function toList(): array;

    /**
     * ```php
     * >>> HashMap::collectPairs([['a',  1], ['b', 2]])->toAssocArray();
     * => Some(['a' => 1, 'b' => 2])
     * >>> HashMap::collectPairs([[new Foo(), 1], [new Foo(), 2]])->toAssocArray();
     * => None
     * ```
     * @psalm-return (TKey is array-key ? array<TKey, TValue> : never)
     */
    public function toAssocArray(): array;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toLinkedList();
     * => LinkedList(['a', 1], ['b', 2])
     * ```
     *
     * @return LinkedList<array{TKey, TValue}>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toArrayList();
     * => ArrayList(['a', 1], ['b', 2])
     * ```
     *
     * @return ArrayList<array{TKey, TValue}>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toHashSet();
     * => HashSet(['a', 1], ['b', 2])
     * ```
     *
     * @return HashSet<array{TKey, TValue}>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toHashMap();
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @return HashMap<TKey, TValue>
     */
    public function toHashMap(): HashMap;
}
