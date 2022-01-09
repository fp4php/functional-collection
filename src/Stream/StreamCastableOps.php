<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream;

use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Immutable\Set\HashSet;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyArrayList;
use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface StreamCastableOps
{
    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->compile()->toArray();
     * => [1, 2, 2]
     * ```
     *
     * @return list<TV>
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
     * @return Option<non-empty-list<TV>>
     */
    public function toNonEmptyList(): Option;

    /**
     * ```php
     * >>> Stream::emits([[1, 'a'], [2, 'b']])->compile()->toAssocArray(fn($pair) => $pair);
     * => [1 => 'a', 2 => 'b']
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @param callable(TV): array{TKO, TVO} $callback
     * @return array<TKO, TVO>
     */
    public function toAssocArray(callable $callback): array;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->compile()->toLinkedList();
     * => LinkedList(1, 2, 2)
     * ```
     *
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->compile()->toArrayList();
     * => ArrayList(1, 2, 2)
     * ```
     *
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->compile()->toNonEmptyArrayList();
     * => Some(NonEmptyArrayList(1, 2, 2))
     *
     * >>> Stream::emits([])->compile()->toNonEmptyArrayList();
     * => None
     * ```
     *
     * @return Option<NonEmptyArrayList<TV>>
     */
    public function toNonEmptyArrayList(): Option;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->compile()->toHashSet();
     * => HashSet(1, 2)
     * ```
     *
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> Stream::emits([1, 2])
     * >>>    ->compile()
     * >>>    ->toHashMap(fn($elem) => [(string) $elem, $elem]);
     * => HashMap('1' -> 1, '2' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap;

    /**
     * @param string $path file path
     * @param bool $append append to an existing file
     */
    public function toFile(string $path, bool $append = false): void;
}
