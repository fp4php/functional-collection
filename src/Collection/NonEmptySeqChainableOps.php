<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface NonEmptySeqChainableOps
{
    /**
     * Add element to the collection end
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->appended(3)->toArray();
     * => [1, 2, 3]
     * ```
     *
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function appended(mixed $elem): NonEmptySeq;

    /**
     * Add elements to the collection end
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->appendedAll([3, 4])->toArray();
     * => [1, 2, 3, 4]
     * ```
     *
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function appendedAll(iterable $suffix): NonEmptySeq;

    /**
     * Add element to the collection start
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->prepended(0)->toArray();
     * => [0, 1, 2]
     * ```
     *
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function prepended(mixed $elem): NonEmptySeq;

    /**
     * Add elements to the collection start
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->prependedAll(-1, 0)->toArray();
     * => [-1, 0, 1, 2]
     * ```
     *
     * @template TVI
     * @psalm-param iterable<TVI> $prefix
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function prependedAll(iterable $prefix): NonEmptySeq;

    /**
     * Filter collection by condition.
     * true - include element to new collection.
     * false - exclude element from new collection.
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->filter(fn($elem) => $elem > 1)->toArray();
     * => [2]
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Seq<TV>
     */
    public function filter(callable $predicate): Seq;

    /**
     * A combined {@see NonEmptySeq::map} and {@see NonEmptySeq::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     * Also, NonEmpty* prefix will be lost.
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return Seq<TVO>
     */
    public function filterMap(callable $callback): Seq;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, null])->filterNotNull()->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-return Seq<TV>
     */
    public function filterNotNull(): Seq;

    /**
     * Filter elements of given class
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, new Foo(2)])->filterOf(Foo::class)->toArray();
     * => [Foo(2)]
     * ```
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Seq<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Seq;

    /**
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toArray();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return Seq<TVO>
     */
    public function flatMap(callable $callback): Seq;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->map(fn($elem) => (string) $elem)->toArray();
     * => ['1', '2']
     * ```
     *
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return NonEmptySeq<TVO>
     */
    public function map(callable $callback): NonEmptySeq;

    /**
     * Copy collection in reversed order
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->reverse()->toArray();
     * => [2, 1]
     * ```
     *
     * @psalm-return NonEmptySeq<TV>
     */
    public function reverse(): NonEmptySeq;

    /**
     * Returns every collection element except first
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->tail()->toArray();
     * => [2, 3]
     * ```
     *
     * @psalm-return Seq<TV>
     */
    public function tail(): Seq;

    /**
     * Returns collection unique elements
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 1, 2])->unique(fn($elem) => $elem)->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-param callable(TV): (int|string) $callback returns element unique id
     * @psalm-return NonEmptySeq<TV>
     */
    public function unique(callable $callback): NonEmptySeq;

    /**
     * Take collection elements while predicate is true
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->takeWhile(fn($e) => $e < 3)->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Seq<TV>
     */
    public function takeWhile(callable $predicate): Seq;

    /**
     * Drop collection elements while predicate is true
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->dropWhile(fn($e) => $e < 3)->toArray();
     * => [3]
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Seq<TV>
     */
    public function dropWhile(callable $predicate): Seq;

    /**
     * Take N collection elements
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->take(2)->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-return Seq<TV>
     */
    public function take(int $length): Seq;

    /**
     * Drop N collection elements
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->drop(2)->toArray();
     * => [3]
     * ```
     *
     * @psalm-return Seq<TV>
     */
    public function drop(int $length): Seq;

    /**
     * Group elements
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 1, 3])
     * >>>     ->groupBy(fn($e) => $e)
     * >>>     ->map(fn(Seq $e) => $e->toArray())
     * >>>     ->toArray();
     * => [[1, [1, 1]], [3, [3]]]
     * ```
     *
     * @template TKO
     * @psalm-param callable(TV): TKO $callback
     * @psalm-return NonEmptyMap<TKO, NonEmptySeq<TV>>
     */
    public function groupBy(callable $callback): NonEmptyMap;

    /**
     * Sort collection
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([2, 1, 3])->sorted(fn($lhs, $rhs) => $lhs - $rhs)->toArray();
     * => [1, 2, 3]
     *
     * >>> NonEmptyLinkedList::collectNonEmpty([2, 1, 3])->sorted(fn($lhs, $rhs) => $rhs - $lhs)->toArray();
     * => [3, 2, 1]
     * ```
     *
     * @psalm-param callable(TV, TV): int $cmp
     * @psalm-return NonEmptySeq<TV>
     */
    public function sorted(callable $cmp): NonEmptySeq;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toArray();
     * => [2, 3]
     * ```
     *
     * @param callable(TV): void $callback
     * @psalm-return NonEmptySeq<TV>
     */
    public function tap(callable $callback): NonEmptySeq;
}
