<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\Immutable\Set\HashSet;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface SetCastableOps
{
    /**
     * ```php
     * >>> HashSet::collect([1, 2, 2])->toArray();
     * => [1, 2]
     * ```
     *
     * @return list<TValue>
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> HashSet::collect([1, 2, 2])->toLinkedList();
     * => LinkedList(1, 2)
     * ```
     *
     * @return LinkedList<TValue>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> HashSet::collect([1, 2, 2])->toArrayList();
     * => ArrayList(1, 2)
     * ```
     *
     * @return ArrayList<TValue>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> HashSet::collect([1, 2, 2])->toHashSet();
     * => HashSet(1, 2)
     * ```
     *
     * @return HashSet<TValue>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> HashSet::collect([1, 2, 2])
     * >>>     ->toHashMap(fn($elem) => [(string) $elem, $elem]);
     * => HashMap('1' -> 1, '2' -> 2)
     * ```
     *
     * @template TKI
     * @template TValueIn
     * @param callable(TValue): array{TKI, TValueIn} $callback
     * @return HashMap<TKI, TValueIn>
     */
    public function toHashMap(callable $callback): HashMap;
}
