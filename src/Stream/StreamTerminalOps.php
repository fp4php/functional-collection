<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream;

use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface StreamTerminalOps
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
     * @psalm-param callable(TV): bool $predicate
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
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
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
     * @psalm-param callable(TV): bool $predicate
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
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
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
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
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
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option;

    /**
     * Fold many elements into one
     *
     * ```php
     * >>> Stream::emits(['1', '2'])->compile()->fold('0', fn($acc, $cur) => $acc . $cur);
     * => '012'
     * ```
     *
     * @template TA
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, TV): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
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
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback (accumulator, current value): new accumulator
     * @psalm-return Option<TV|TA>
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
     * @psalm-return Option<TV>
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
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
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
     * @psalm-return Option<TV>
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
     * @psalm-return Option<TV>
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
}
