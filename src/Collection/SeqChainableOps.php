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
     * >>> LinkedList::collect([1, 2])->appended(3)->toArray();
     * => [1, 2, 3]
     * ```
     *
     * @template TValueIn
     * @psalm-param TValueIn $elem
     * @psalm-return Seq<TValue|TValueIn>
     */
    public function appended(mixed $elem): Seq;

    /**
     * Add elements to the collection end
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->appendedAll([3, 4])->toArray();
     * => [1, 2, 3, 4]
     * ```
     *
     * @template TValueIn
     * @psalm-param iterable<TValueIn> $suffix
     * @psalm-return Seq<TValue|TValueIn>
     */
    public function appendedAll(iterable $suffix): Seq;

    /**
     * Add element to the collection start
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->prepended(0)->toArray();
     * => [0, 1, 2]
     * ```
     *
     * @template TValueIn
     * @psalm-param TValueIn $elem
     * @psalm-return Seq<TValue|TValueIn>
     */
    public function prepended(mixed $elem): Seq;

    /**
     * Add elements to the collection start
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->prependedAll(-1, 0)->toArray();
     * => [-1, 0, 1, 2]
     * ```
     *
     * @template TValueIn
     * @psalm-param iterable<TValueIn> $prefix
     * @psalm-return Seq<TValue|TValueIn>
     */
    public function prependedAll(iterable $prefix): Seq;

    /**
     * Filter collection by condition.
     * true - include element to new collection.
     * false - exclude element from new collection.
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->filter(fn($elem) => $elem > 1)->toArray();
     * => [2]
     * ```
     *
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return Seq<TValue>
     */
    public function filter(callable $predicate): Seq;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> LinkedList::collect([1, 2, null])->filterNotNull()->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-return Seq<TValue>
     */
    public function filterNotNull(): Seq;

    /**
     * Filter elements of given class
     *
     * ```php
     * >>> LinkedList::collect([1, new Foo(2)])->filterOf(Foo::class)->toArray();
     * => [Foo(2)]
     * ```
     *
     * @psalm-template TValueOut
     * @psalm-param class-string<TValueOut> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Seq<TValueOut>
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
     * >>>     ->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-template TValueOut
     * @psalm-param callable(TValue): Option<TValueOut> $callback
     * @psalm-return Seq<TValueOut>
     */
    public function filterMap(callable $callback): Seq;

    /**
     * Map collection and then flatten the result
     *
     * ```php
     * >>> LinkedList::collect([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toArray();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @psalm-template TValueOut
     * @psalm-param callable(TValue): iterable<TValueOut> $callback
     * @psalm-return Seq<TValueOut>
     */
    public function flatMap(callable $callback): Seq;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->map(fn($elem) => (string) $elem)->toArray();
     * => ['1', '2']
     * ```
     *
     * @template TValueOut
     * @psalm-param callable(TValue): TValueOut $callback
     * @psalm-return Seq<TValueOut>
     */
    public function map(callable $callback): Seq;

    /**
     * Copy collection in reversed order
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->reverse()->toArray();
     * => [2, 1]
     * ```
     *
     * @psalm-return Seq<TValue>
     */
    public function reverse(): Seq;

    /**
     * Returns every collection element except first
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->tail()->toArray();
     * => [2, 3]
     * ```
     *
     * @psalm-return Seq<TValue>
     */
    public function tail(): Seq;

    /**
     * Returns collection unique elements
     *
     * ```php
     * >>> LinkedList::collect([1, 1, 2])->unique(fn($elem) => $elem)->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-param callable(TValue): array-key $callback returns element unique id
     * @psalm-return Seq<TValue>
     */
    public function unique(callable $callback): Seq;

    /**
     * Take collection elements while predicate is true
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->takeWhile(fn($e) => $e < 3)->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return Seq<TValue>
     */
    public function takeWhile(callable $predicate): Seq;

    /**
     * Drop collection elements while predicate is true
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->dropWhile(fn($e) => $e < 3)->toArray();
     * => [3]
     * ```
     *
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return Seq<TValue>
     */
    public function dropWhile(callable $predicate): Seq;

    /**
     * Take N collection elements
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->take(2)->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-return Seq<TValue>
     */
    public function take(int $length): Seq;

    /**
     * Drop N collection elements
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->drop(2)->toArray();
     * => [3]
     * ```
     *
     * @psalm-return Seq<TValue>
     */
    public function drop(int $length): Seq;

    /**
     * Sort collection
     *
     * ```php
     * >>> LinkedList::collect([2, 1, 3])->sorted(fn($lhs, $rhs) => $lhs - $rhs)->toArray();
     * => [1, 2, 3]
     *
     * >>> LinkedList::collect([2, 1, 3])->sorted(fn($lhs, $rhs) => $rhs - $lhs)->toArray();
     * => [3, 2, 1]
     * ```
     *
     * @psalm-param callable(TValue, TValue): int $cmp
     * @psalm-return Seq<TValue>
     */
    public function sorted(callable $cmp): Seq;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> LinkedList::collect([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toArray();
     * => [2, 3]
     * ```
     *
     * @param callable(TValue): void $callback
     * @psalm-return Seq<TValue>
     */
    public function tap(callable $callback): Seq;

    /**
     * Group elements
     *
     * ```php
     * >>> LinkedList::collect([1, 1, 3])
     * >>>     ->groupBy(fn($e) => $e)
     * >>>     ->map(fn(Seq $e) => $e->toArray())
     * >>>     ->toArray();
     * => [[1, [1, 1]], [3, [3]]]
     * ```
     *
     * @template TKO
     * @psalm-param callable(TValue): TKO $callback
     * @psalm-return Map<TKO, Seq<TValue>>
     */
    public function groupBy(callable $callback): Map;

    /**
     * Add specified separator between every pair of elements in the source collection.
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->intersperse(0)->toArray();
     * => [1, 0, 2, 0, 3]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $separator
     * @psalm-return Seq<TValue|TValueIn>
     */
    public function intersperse(mixed $separator): Seq;

    /**
     * Deterministically zips elements, terminating when the end of either branch is reached naturally.
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->zip([4, 5, 6, 7])->toArray();
     * => [[1, 4], [2, 5], [3, 6]]
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $that
     * @return Seq<array{TValue, TValueIn}>
     */
    public function zip(iterable $that): Seq;
}
