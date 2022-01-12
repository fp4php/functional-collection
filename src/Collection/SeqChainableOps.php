<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface SeqChainableOps
{
    /**
     * Add element to the collection end
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->appended(3)->toList();
     * => [1, 2, 3]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $elem
     * @return Seq<TValue|TValueIn>
     */
    public function appended(mixed $elem): Seq;

    /**
     * Add elements to the collection end
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->appendedAll([3, 4])->toList();
     * => [1, 2, 3, 4]
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $suffix
     * @return Seq<TValue|TValueIn>
     */
    public function appendedAll(iterable $suffix): Seq;

    /**
     * Add element to the collection start
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->prepended(0)->toList();
     * => [0, 1, 2]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $elem
     * @return Seq<TValue|TValueIn>
     */
    public function prepended(mixed $elem): Seq;

    /**
     * Add elements to the collection start
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->prependedAll(-1, 0)->toList();
     * => [-1, 0, 1, 2]
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $prefix
     * @return Seq<TValue|TValueIn>
     */
    public function prependedAll(iterable $prefix): Seq;

    /**
     * Filter collection by condition.
     * true - include element to new collection.
     * false - exclude element from new collection.
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->filter(fn($elem) => $elem > 1)->toList();
     * => [2]
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return Seq<TValue>
     */
    public function filter(callable $predicate): Seq;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> LinkedList::collect([1, 2, null])->filterNotNull()->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TValue>
     */
    public function filterNotNull(): Seq;

    /**
     * Filter elements of given class
     *
     * ```php
     * >>> LinkedList::collect([1, new Foo(2)])->filterOf(Foo::class)->toList();
     * => [Foo(2)]
     * ```
     *
     * @template TValueOut
     * @param class-string<TValueOut> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     * @return Seq<TValueOut>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Seq;

    /**
     * A combined {@see Seq::map} and {@see Seq::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TValueOut can be different from the input type TValue.
     *
     * ```php
     * >>> LinkedList::collect(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toList();
     * => [1, 2]
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): Option<TValueOut> $callback
     * @return Seq<TValueOut>
     */
    public function filterMap(callable $callback): Seq;

    /**
     * Map collection and then flatten the result
     *
     * ```php
     * >>> LinkedList::collect([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toList();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): iterable<TValueOut> $callback
     * @return Seq<TValueOut>
     */
    public function flatMap(callable $callback): Seq;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->map(fn($elem) => (string) $elem)->toList();
     * => ['1', '2']
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): TValueOut $callback
     * @return Seq<TValueOut>
     */
    public function map(callable $callback): Seq;

    /**
     * Copy collection in reversed order
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->reverse()->toList();
     * => [2, 1]
     * ```
     *
     * @return Seq<TValue>
     */
    public function reverse(): Seq;

    /**
     * Returns every collection element except first
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->tail()->toList();
     * => [2, 3]
     * ```
     *
     * @return Seq<TValue>
     */
    public function tail(): Seq;

    /**
     * Returns collection unique elements
     *
     * ```php
     * >>> LinkedList::collect([1, 1, 2])->unique(fn($elem) => $elem)->toList();
     * => [1, 2]
     * ```
     *
     * @param callable(TValue): array-key $callback returns element unique id
     * @return Seq<TValue>
     */
    public function unique(callable $callback): Seq;

    /**
     * Take collection elements while predicate is true
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->takeWhile(fn($e) => $e < 3)->toList();
     * => [1, 2]
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return Seq<TValue>
     */
    public function takeWhile(callable $predicate): Seq;

    /**
     * Drop collection elements while predicate is true
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->dropWhile(fn($e) => $e < 3)->toList();
     * => [3]
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return Seq<TValue>
     */
    public function dropWhile(callable $predicate): Seq;

    /**
     * Take N collection elements
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->take(2)->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TValue>
     */
    public function take(int $length): Seq;

    /**
     * Drop N collection elements
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->drop(2)->toList();
     * => [3]
     * ```
     *
     * @return Seq<TValue>
     */
    public function drop(int $length): Seq;

    /**
     * Sort collection
     *
     * ```php
     * >>> LinkedList::collect([2, 1, 3])->sorted(fn($lhs, $rhs) => $lhs - $rhs)->toList();
     * => [1, 2, 3]
     *
     * >>> LinkedList::collect([2, 1, 3])->sorted(fn($lhs, $rhs) => $rhs - $lhs)->toList();
     * => [3, 2, 1]
     * ```
     *
     * @param callable(TValue, TValue): int $cmp
     * @return Seq<TValue>
     */
    public function sorted(callable $cmp): Seq;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> LinkedList::collect([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toList();
     * => [2, 3]
     * ```
     *
     * @param callable(TValue): void $callback
     * @return Seq<TValue>
     */
    public function tap(callable $callback): Seq;

    /**
     * Group elements
     *
     * ```php
     * >>> LinkedList::collect([1, 1, 3])
     * >>>     ->groupBy(fn($e) => $e)
     * >>>     ->map(fn(Seq $e) => $e->toList())
     * >>>     ->toList();
     * => [[1, [1, 1]], [3, [3]]]
     * ```
     *
     * @template TKO
     * @param callable(TValue): TKO $callback
     * @return Map<TKO, Seq<TValue>>
     */
    public function groupBy(callable $callback): Map;

    /**
     * Add specified separator between every pair of elements in the source collection.
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->intersperse(0)->toList();
     * => [1, 0, 2, 0, 3]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $separator
     * @return Seq<TValue|TValueIn>
     */
    public function intersperse(mixed $separator): Seq;

    /**
     * Deterministically zips elements, terminating when the end of either branch is reached naturally.
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->zip([4, 5, 6, 7])->toList();
     * => [[1, 4], [2, 5], [3, 6]]
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $that
     * @return Seq<array{TValue, TValueIn}>
     */
    public function zip(iterable $that): Seq;
}
