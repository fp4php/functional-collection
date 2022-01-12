<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface SetTerminalOps
{
    /**
     * Check if the element is present in the set
     * Alias for {@see SetOps::contains}
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2])(1);
     * => true
     *
     * >>> HashSet::collect([1, 1, 2])(3);
     * => false
     * ```
     *
     * @param TValue $element
     */
    public function __invoke(mixed $element): bool;

    /**
     * Check if the element is present in the set
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2])->contains(1);
     * => true
     *
     * >>> HashSet::collect([1, 1, 2])->contains(3);
     * => false
     * ```
     *
     * @param TValue $element
     */
    public function contains(mixed $element): bool;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> HashSet::collect([1, 2, 2])->every(fn($elem) => $elem > 0);
     * => true
     *
     * >>> HashSet::collect([1, 2, 2])->every(fn($elem) => $elem > 1);
     * => false
     * ```
     *
     * @param callable(TValue): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Returns true if every collection element is of given class
     * false otherwise
     *
     * ```php
     * >>> HashSet::collect([new Foo(1), new Foo(2)])->everyOf(Foo::class);
     * => true
     *
     * >>> HashSet::collect([new Foo(1), new Bar(2)])->everyOf(Foo::class);
     * => false
     * ```
     *
     * @template TValueOut
     * @param class-string<TValueOut> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool;

    /**
     * A combined {@see Set::map} and {@see Set::every}.
     *
     * Predicate satisfying is handled via Option instead of Boolean.
     * So the output type TValueOut can be different from the input type TValue.
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(HashSet(1, 2, 3))
     *
     * >>> HashSet::collect([0, 1, 2])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): Option<TValueOut> $callback
     * @return Option<Set<TValueOut>>
     */
    public function everyMap(callable $callback): Option;

    /**
     * Find if there is element which satisfies the condition
     *
     * ```php
     * >>> HashSet::collect([1, 2, 2])->exists(fn($elem) => 2 === $elem);
     * => true
     *
     * >>> HashSet::collect([1, 2, 2])->exists(fn($elem) => 3 === $elem);
     * => false
     * ```
     *
     * @param callable(TValue): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Returns true if there is collection element of given class
     * False otherwise
     *
     * ```php
     * >>> HashSet::collect([1, new Foo(2)])->existsOf(Foo::class);
     * => true
     *
     * >>> HashSet::collect([1, new Foo(2)])->existsOf(Bar::class);
     * => false
     * ```
     *
     * @template TValueOut
     * @param class-string<TValueOut> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Fold many elements into one
     *
     * ```php
     * >>> HashSet::collect(['1', '2', '2'])->fold('0', fn($acc, $cur) => $acc . $cur);
     * => '012'
     * ```
     *
     * @template TA
     * @param TA $init initial accumulator value
     * @param callable(TA, TValue): TA $callback (accumulator, current element): new accumulator
     * @return TA
     */
    public function fold(mixed $init, callable $callback): mixed;

    /**
     * Reduce multiple elements into one
     * Returns None for empty collection
     *
     * ```php
     * >>> HashSet::collect(['1', '2', '2'])->reduce(fn($acc, $cur) => $acc . $cur)->get();
     * => '12'
     * ```
     *
     * @template TA
     * @param callable(TValue|TA, TValue): (TValue|TA) $callback (accumulator, current value): new accumulator
     * @return Option<TValue|TA>
     */
    public function reduce(callable $callback): Option;

    /**
     * Check if this set is subset of another set
     *
     * ```php
     * >>> HashSet::collect([1, 2])->subsetOf(HashSet::collect([1, 2]));
     * => true
     *
     * >>> HashSet::collect([1, 2])->subsetOf(HashSet::collect([1, 2, 3]));
     * => true
     *
     * >>> HashSet::collect([1, 2, 3])->subsetOf(HashSet::collect([1, 2]));
     * => false
     * ```
     */
    public function subsetOf(Set $superset): bool;

    /**
     * Find first element which satisfies the condition
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])->first(fn($elem) => $elem > 1)->get();
     * => 2
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return Option<TValue>
     */
    public function first(callable $predicate): Option;

    /**
     * Returns last collection element which satisfies the condition
     *
     * ```php
     * >>> HashSet::collect([1, 0, 2])->last(fn($elem) => $elem > 0)->get();
     * => 2
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return Option<TValue>
     */
    public function last(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * ```php
     * >>> HashSet::collect([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get();
     * => Foo(2)
     * ```
     *
     * @template TValueOut
     * @param class-string<TValueOut> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     * @return Option<TValueOut>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option;

    /**
     * Return first collection element
     *
     * ```php
     * >>> HashSet::collect([1, 2])->head()->get();
     * => 1
     * ```
     *
     * @return Option<TValue>
     */
    public function head(): Option;

    /**
     * Returns first collection element
     * Alias for {@see SetOps::head}
     *
     * ```php
     * >>> HashSet::collect([1, 2])->firstElement()->get();
     * => 1
     * ```
     *
     * @return Option<TValue>
     */
    public function firstElement(): Option;

    /**
     * Returns last collection element
     *
     * ```php
     * >>> HashSet::collect([1, 2])->lastElement()->get();
     * => 2
     * ```
     *
     * @return Option<TValue>
     */
    public function lastElement(): Option;

    /**
     * Check if at least one collection element is present
     */
    public function isNonEmpty(): bool;

    /**
     * Check if there are no elements in collection
     */
    public function isEmpty(): bool;
}
