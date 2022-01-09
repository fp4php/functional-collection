<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface NonEmptySetChainableOps
{
    /**
     * Produces new set with given element included
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->updated(3)->toArray();
     * => [1, 2, 3]
     * ```
     *
     * @template TVI
     * @param TVI $element
     * @return NonEmptySet<TV|TVI>
     */
    public function updated(mixed $element): NonEmptySet;

    /**
     * Produces new set with given element excluded
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->removed(2)->toArray();
     * => [1]
     * ```
     *
     * @param TV $element
     * @return Set<TV>
     */
    public function removed(mixed $element): Set;

    /**
     * Filter collection by condition
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->filter(fn($elem) => $elem > 1)->toArray();
     * => [2]
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Set<TV>
     */
    public function filter(callable $predicate): Set;

    /**
     * Filter elements of given class
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, new Foo(2)])->filterOf(Foo::class)->toArray();
     * => [Foo(2)]
     * ```
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Set<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Set;

    /**
     * A combined {@see NonEmptySet::map} and {@see NonEmptySet::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     * Also, NonEmpty* prefix will be lost.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toArray();
     * => [1, 2]
     * ```
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return Set<TVO>
     */
    public function filterMap(callable $callback): Set;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, null])->filterNotNull()->toArray();
     * => [1]
     * ```
     *
     * @psalm-return Set<TV>
     */
    public function filterNotNull(): Set;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->map(fn($elem) => (string) $elem)->toArray();
     * => ['1', '2']
     * ```
     *
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return NonEmptySet<TVO>
     */
    public function map(callable $callback): NonEmptySet;

    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([2, 5])
     * >>>     ->flatMap(fn($e) => [$e - 1, $e, $e, $e + 1])
     * >>>     ->toArray();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return Set<TVO>
     */
    public function flatMap(callable $callback): Set;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toArray();
     * => [2, 3]
     * ```
     *
     * @param callable(TV): void $callback
     * @psalm-return NonEmptySet<TV>
     */
    public function tap(callable $callback): NonEmptySet;

    /**
     * Returns every collection element except first
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->tail()->toArray();
     * => [2, 3]
     * ```
     *
     * @psalm-return Set<TV>
     */
    public function tail(): Set;

    /**
     * Computes the intersection between this set and another set.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])
     *     ->intersect(HashSet::collect([2, 3]))->toArray();
     * => [2, 3]
     * ```
     *
     * @param Set<TV>|NonEmptySet<TV> $that the set to intersect with.
     * @return Set<TV> a new set consisting of all elements that are both in this
     * set and in the given set `that`.
     */
    public function intersect(Set|NonEmptySet $that): Set;

    /**
     * Computes the difference of this set and another set.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])
     *     ->diff(HashSet::collect([2, 3]))->toArray();
     * => [1]
     * ```
     *
     * @param Set<TV>|NonEmptySet<TV> $that the set of elements to exclude.
     * @return Set<TV> a set containing those elements of this
     * set that are not also contained in the given set `that`.
     */
    public function diff(Set|NonEmptySet $that): Set;
}
