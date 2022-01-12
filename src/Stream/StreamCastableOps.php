<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream;

use Generator;
use Whsv26\Functional\Collection\Map\HashMap;
use Whsv26\Functional\Collection\Seq\ArrayList;
use Whsv26\Functional\Collection\Seq\LinkedList;
use Whsv26\Functional\Collection\Set\HashSet;
use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface StreamCastableOps
{
    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->compile()->toArray();
     * => [1, 2, 2]
     * ```
     *
     * @return list<TValue>
     */
    public function toList(): array;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->compile()->toNonEmptyList();
     * => Some([1, 2, 2])
     *
     * >>> Stream::emits([])->compile()->toNonEmptyList();
     * => None
     * ```
     *
     * @return Option<non-empty-list<TValue>>
     */
    public function toNonEmptyList(): Option;

    /**
     * ```php
     * >>> Stream::emits([[1, 'a'], [2, 'b']])->compile()->toArray();
     * => [1 => 'a', 2 => 'b']
     * ```
     *
     * @template TKeyIn of array-key
     * @template TValueIn
     * @psalm-if-this-is StreamCastableOps<array{TKeyIn, TValueIn}>
     * @psalm-return array<TKeyIn, TValueIn>
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> Stream::emits([[1, 'a'], [2, 'b']])->compile()->toNonEmptyAssocArray();
     * => Some([1 => 'a', 2 => 'b'])
     *
     * >>> Stream::emits([])->compile()->toNonEmptyArray();
     * => None
     * ```
     *
     * @template TKeyIn of array-key
     * @template TValueIn
     * @psalm-if-this-is StreamCastableOps<array{TKeyIn, TValueIn}>
     * @psalm-return Option<non-empty-array<TKeyIn, TValueIn>>
     */
    public function toNonEmptyArray(): Option;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->compile()->toLinkedList();
     * => LinkedList(1, 2, 2)
     * ```
     *
     * @return LinkedList<TValue>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->compile()->toArrayList();
     * => ArrayList(1, 2, 2)
     * ```
     *
     * @return ArrayList<TValue>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->compile()->toHashSet();
     * => HashSet(1, 2)
     * ```
     *
     * @return HashSet<TValue>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> Stream::emits([['a', 1], ['b', 2]])
     * >>>    ->compile()
     * >>>    ->toHashMap();
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @psalm-if-this-is StreamCastableOps<array{TKeyIn, TValueIn}>
     * @psalm-return HashMap<TKeyIn, TValueIn>
     */
    public function toHashMap(): HashMap;

    /**
     * ```php
     * >>> $gen = Stream::emits([1, 2, 2])->compile()->toGenerator();
     * >>> Stream::emits($gen)->compile()->toList();
     * => [1, 2, 2]
     * ```
     *
     * @return Generator<TValue>
     */
    public function toGenerator(): Generator;

    /**
     * @param string $path file path
     * @param bool $append append to an existing file
     */
    public function toFile(string $path, bool $append = false): void;
}
