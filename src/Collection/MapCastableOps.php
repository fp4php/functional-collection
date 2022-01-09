<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Fp\Functional\Option\None;
use Whsv26\Functional\Core\Option;
use Fp\Functional\Option\Some;
use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\Immutable\Set\HashSet;

/**
 * @psalm-immutable
 * @template TK
 * @template-covariant TV
 */
interface MapCastableOps
{
    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toArray();
     * => [['a', 1], ['b', 2]]
     * ```
     *
     * @return list<array{TK, TV}>
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> HashMap::collectPairs([['a',  1], ['b', 2]])->toAssocArray();
     * => Some(['a' => 1, 'b' => 2])
     * >>> HashMap::collectPairs([[new Foo(), 1], [new Foo(), 2]])->toAssocArray();
     * => None
     * ```
     * @psalm-return (TK is array-key ? array<TK, TV> : never)
     */
    public function toAssocArray(): array;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toLinkedList();
     * => LinkedList(['a', 1], ['b', 2])
     * ```
     *
     * @return LinkedList<array{TK, TV}>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toArrayList();
     * => ArrayList(['a', 1], ['b', 2])
     * ```
     *
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toHashSet();
     * => HashSet(['a', 1], ['b', 2])
     * ```
     *
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toHashMap();
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap;
}
