<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface SetChainableOps
{
    /**
     * Produces new set with given element included
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2])->updated(3)->toList();
     * => [1, 2, 3]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $element
     * @return Set<TValue|TValueIn>
     */
    public function updated(mixed $element): Set;

    /**
     * Produces new set with given element excluded
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2])->removed(2)->toList();
     * => [1]
     * ```
     *
     * @param TValue $element
     * @return Set<TValue>
     */
    public function removed(mixed $element): Set;

    /**
     * Filter collection by condition
     *
     * ```php
     * >>> HashSet::collect([1, 2, 2])->filter(fn($elem) => $elem > 1)->toList();
     * => [2]
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return Set<TValue>
     */
    public function filter(callable $predicate): Set;

    /**
     * Filter elements of given class
     *
     * ```php
     * >>> HashSet::collect([1, 1, new Foo(2)])->filterOf(Foo::class)->toList();
     * => [Foo(2)]
     * ```
     *
     * @template TValueOut
     * @param class-string<TValueOut> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     * @return Set<TValueOut>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Set;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> HashSet::collect([1, 1, null])->filterNotNull()->toList();
     * => [1]
     * ```
     *
     * @return Set<TValue>
     */
    public function filterNotNull(): Set;

    /**
     * A combined {@see Set::map} and {@see Set::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TValueOut can be different from the input type TValue.
     *
     * ```php
     * >>> HashSet::collect(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toList();
     * => [1, 2]
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): Option<TValueOut> $callback
     * @return Set<TValueOut>
     */
    public function filterMap(callable $callback): Set;

    /**
     * ```php
     * >>> HashSet::collect([2, 5, 5])->flatMap(fn($e) => [$e - 1, $e, $e, $e + 1])->toList();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): iterable<TValueOut> $callback
     * @return Set<TValueOut>
     */
    public function flatMap(callable $callback): Set;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> HashSet::collect([1, 2, 2])->map(fn($elem) => (string) $elem)->toList();
     * => ['1', '2']
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): TValueOut $callback
     * @return Set<TValueOut>
     */
    public function map(callable $callback): Set;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> HashSet::collect([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toList();
     * => [2, 3]
     * ```
     *
     * @param callable(TValue): void $callback
     * @return Set<TValue>
     */
    public function tap(callable $callback): Set;

    /**
     * Returns every collection element except first
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])->tail()->toList();
     * => [2, 3]
     * ```
     *
     * @return Set<TValue>
     */
    public function tail(): Set;

    /**
     * Computes the intersection between this set and another set.
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])
     *     ->intersect(HashSet::collect([2, 3]))->toList();
     * => [2, 3]
     * ```
     *
     * @param Set<TValue> $that the set to intersect with.
     * @return Set<TValue> a new set consisting of all elements that are both in this
     * set and in the given set `that`.
     */
    public function intersect(Set $that): Set;

    /**
     * Computes the difference of this set and another set.
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])
     *     ->diff(HashSet::collect([2, 3]))->toList();
     * => [1]
     * ```
     *
     * @param Set<TValue> $that the set of elements to exclude.
     * @return Set<TValue> a set containing those elements of this
     * set that are not also contained in the given set `that`.
     */
    public function diff(Set $that): Set;
}
