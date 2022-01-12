<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream;

use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface CompiledStreamTerminalOps
{
    /**
     * Returns true if every stream element satisfy the condition
     * and false otherwise
     *
     * ```php
     * >>> Stream::emits([1, 2])->compile()->every(fn($elem) => $elem > 0);
     * => true
     *
     * >>> Stream::emits([1, 2])->compile()->every(fn($elem) => $elem > 1);
     * => false
     * ```
     *
     * @param callable(TValue): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Returns true if every stream element is of given class
     * false otherwise
     *
     * ```php
     * >>> Stream::emits([new Foo(1), new Foo(2)])->compile()->everyOf(Foo::class);
     * => true
     *
     * >>> Stream::emits([new Foo(1), new Bar(2)])->compile()->everyOf(Foo::class);
     * => false
     * ```
     *
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Find if there is element which satisfies the condition
     *
     * ```php
     * >>> Stream::emits([1, 2])->compile()->exists(fn($elem) => 2 === $elem);
     * => true
     *
     * >>> Stream::emits([1, 2])->compile()->exists(fn($elem) => 3 === $elem);
     * => false
     * ```
     *
     * @param callable(TValue): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Returns true if there is stream element of given class
     * False otherwise
     *
     * ```php
     * >>> Stream::emits([1, new Foo(2)])->compile()->existsOf(Foo::class);
     * => true
     *
     * >>> Stream::emits([1, new Foo(2)])->compile()->existsOf(Bar::class);
     * => false
     * ```
     *
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Find first element which satisfies the condition
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->compile()->first(fn($elem) => $elem > 1)->get();
     * => 2
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return Option<TValue>
     */
    public function first(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * ```php
     * >>> Stream::emits([new Bar(1), new Foo(2), new Foo(3)])->compile()->firstOf(Foo::class)->get();
     * => Foo(2)
     * ```
     *
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     * @return Option<TValueIn>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option;

    /**
     * Find first element of given class
     *
     * ```php
     * >>> Stream::emits([new Bar(1), new Foo(2), new Foo(3)])->compile()->lastOf(Foo::class)->get();
     * => Foo(3)
     * ```
     *
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     * @return Option<TValueIn>
     */
    public function lastOf(string $fqcn, bool $invariant = false): Option;

    /**
     * Fold many elements into one
     *
     * ```php
     * >>> Stream::emits(['1', '2'])->compile()->fold('0', fn($acc, $cur) => $acc . $cur);
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
     * Returns None for empty stream
     *
     * ```php
     * >>> Stream::emits(['1', '2'])->compile()->reduce(fn($acc, $cur) => $acc . $cur)->get();
     * => '12'
     * ```
     *
     * @template TA
     * @param callable(TValue|TA, TValue): (TValue|TA) $callback (accumulator, current value): new accumulator
     * @return Option<TValue|TA>
     */
    public function reduce(callable $callback): Option;

    /**
     * Return first stream element
     *
     * ```php
     * >>> Stream::emits([1, 2])->compile()->head()->get();
     * => 1
     * ```
     *
     * @return Option<TValue>
     */
    public function head(): Option;

    /**
     * Returns last stream element which satisfies the condition
     *
     * ```php
     * >>> Stream::emits([1, 0, 2])->compile()->last(fn($elem) => $elem > 0)->get();
     * => 2
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return Option<TValue>
     */
    public function last(callable $predicate): Option;

    /**
     * Returns first stream element
     * Alias for {@see SeqOps::head}
     *
     * ```php
     * >>> Stream::emits([1, 2])->compile()->firstElement()->get();
     * => 1
     * ```
     *
     * @return Option<TValue>
     */
    public function firstElement(): Option;

    /**
     * Returns last stream element
     *
     * ```php
     * >>> Stream::emits([1, 2])->compile()->lastElement()->get();
     * => 2
     * ```
     *
     * @return Option<TValue>
     */
    public function lastElement(): Option;

    /**
     * Run the stream.
     *
     * This is useful if you care only for side effects.
     *
     * ```php
     * >>> Stream::emits([1, 2])->lines()->compile()->drain();
     * 1
     * 2
     * ```
     */
    public function drain(): void;

    /**
     * Displays all elements of this collection in a string
     * using start, end, and separator strings.
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->compile()->mkString("(", ",", ")")
     * => '(1,2,3)'
     *
     * >>> Stream::emits([])->compile()->mkString("(", ",", ")")
     * => '()'
     * ```
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string;

    /**
     * A combined {@see Stream::map} and {@see Stream::every}.
     *
     * Predicate satisfying is handled via Option instead of Boolean.
     * So the output type TValueOut can be different from the input type TValue.
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(Stream(1, 2, 3))
     *
     * >>> Stream::emits([0, 1, 2])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): Option<TValueOut> $callback
     * @return Option<Stream<TValueOut>>
     */
    public function everyMap(callable $callback): Option;

    /**
     * Find element by its index (Starts from zero)
     * Returns None if there is no such element
     *
     * ```php
     * >>> Stream::emits([1, 2])->compile()->at(1)->get();
     * => 2
     * ```
     * @return Option<TValue>
     */
    public function at(int $index): Option;

    /**
     * Count streamed elements
     *
     * ```php
     * >>> Stream::emits([1, 2])->compile()->count();
     * => 2
     * ```
     */
    public function count(): int;
}
