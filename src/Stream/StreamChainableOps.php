<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream;

use Whsv26\Functional\Collection\Seq;
use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface StreamChainableOps
{
    /**
     * Add element to the stream end
     *
     * ```php
     * >>> Stream::emits([1, 2])->appended(3)->compile()->toArray();
     * => [1, 2, 3]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $elem
     * @return self<TValue|TValueIn>
     */
    public function appended(mixed $elem): self;

    /**
     * Add elements to the stream end
     *
     * ```php
     * >>> Stream::emits([1, 2])->appendedAll([3, 4])->compile()->toArray();
     * => [1, 2, 3, 4]
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $suffix
     * @return self<TValue|TValueIn>
     */
    public function appendedAll(iterable $suffix): self;

    /**
     * Add element to the stream start
     *
     * ```php
     * >>> Stream::emits([1, 2])->prepended(0)->compile()->toArray();
     * => [0, 1, 2]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $elem
     * @return self<TValue|TValueIn>
     */
    public function prepended(mixed $elem): self;

    /**
     * Add elements to the stream start
     *
     * ```php
     * >>> Stream::emits([1, 2])->prependedAll(-1, 0)->compile()->toArray();
     * => [-1, 0, 1, 2]
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $prefix
     * @return self<TValue|TValueIn>
     */
    public function prependedAll(iterable $prefix): self;

    /**
     * Filter stream by condition.
     * true - include element to new stream.
     * false - exclude element from new stream.
     *
     * ```php
     * >>> Stream::emits([1, 2])->filter(fn($elem) => $elem > 1)->compile()->toArray();
     * => [2]
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return self<TValue>
     */
    public function filter(callable $predicate): self;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> Stream::emits([1, 2, null])->filterNotNull()->compile()->toArray();
     * => [1, 2]
     * ```
     *
     * @return self<TValue>
     */
    public function filterNotNull(): self;

    /**
     * Filter elements of given class
     *
     * ```php
     * >>> Stream::emits([1, new Foo(2)])->filterOf(Foo::class)->compile()->toArray();
     * => [Foo(2)]
     * ```
     *
     * @template TValueOut
     * @param class-string<TValueOut> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     * @return self<TValueOut>
     */
    public function filterOf(string $fqcn, bool $invariant = false): self;

    /**
     * A combined {@see Stream::map} and {@see Stream::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TValueOut can be different from the input type TValue.
     *
     * ```php
     * >>> Stream::emits(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->compile()->toArray();
     * => [1, 2]
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): Option<TValueOut> $callback
     * @return self<TValueOut>
     */
    public function filterMap(callable $callback): self;

    /**
     * Map stream and then flatten the result
     *
     * ```php
     * >>> Stream::emits([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->compile()->toArray();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): iterable<TValueOut> $callback
     * @return self<TValueOut>
     */
    public function flatMap(callable $callback): self;

    /**
     * Produces a new stream of elements by mapping each element in stream
     * through a transformation function (callback)
     *
     * ```php
     * >>> Stream::emits([1, 2])->map(fn($elem) => (string) $elem)->compile()->toArray();
     * => ['1', '2']
     * ```
     *
     * @template TValueOut
     * @param callable(TValue): TValueOut $callback
     * @return self<TValueOut>
     */
    public function map(callable $callback): self;

    /**
     * Produces a new stream of elements by mapping each key in stream of pairs
     * through a transformation function (callback)
     *
     * ```php
     * >>> Stream::emits(['a', 'b'])->mapKeys(fn($key) => $key + 1)->compile()->toArray();
     * => [1 => 'a', 2 => 'b']
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @template TKeyOut
     *
     * @psalm-if-this-is StreamChainableOps<array{TKeyIn, TValueIn}>
     *
     * @param callable(TKeyIn): TKeyOut $callback
     * @return self<array{TKeyOut, TValueIn}>
     */
    public function mapKeys(callable $callback): self;

    /**
     * Produces a new stream of elements by mapping each value in stream of pairs
     * through a transformation function (callback)
     *
     * ```php
     * >>> Stream::emits([new Foo(1), new Foo(2)])->mapValues(fn(Foo $foo) => $foo->field)->compile()->toArray();
     * => [1, 2]
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @template TValueOut
     *
     * @psalm-if-this-is StreamChainableOps<array{TKeyIn, TValueIn}>
     *
     * @param callable(TValueIn): TValueOut $callback
     * @return self<array{TKeyIn, TValueOut}>
     */
    public function mapValues(callable $callback): self;

    /**
     * @template TKeyIn
     * @template TValueIn
     * @psalm-if-this-is StreamChainableOps<array{TKeyIn, TValueIn}>
     * @param callable(TKeyIn): bool $callback
     * @return self<array{TKeyIn, TValueIn}>
     */
    public function filterKeys(callable $callback): self;

    /**
     * @template TKeyIn
     * @template TValueIn
     * @psalm-if-this-is StreamChainableOps<array{TKeyIn, TValueIn}>
     * @param callable(TValueIn): bool $callback
     * @return self<array{TKeyIn, TValueIn}>
     */
    public function filterValues(callable $callback): self;

    /**
     * Returns every stream element except first
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->tail()->compile()->toArray();
     * => [2, 3]
     * ```
     *
     * @return self<TValue>
     */
    public function tail(): self;

    /**
     * Take stream elements while predicate is true
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->takeWhile(fn($e) => $e < 3)->compile()->toArray();
     * => [1, 2]
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return self<TValue>
     */
    public function takeWhile(callable $predicate): self;

    /**
     * Drop stream elements while predicate is true
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->dropWhile(fn($e) => $e < 3)->compile()->toArray();
     * => [3]
     * ```
     *
     * @param callable(TValue): bool $predicate
     * @return self<TValue>
     */
    public function dropWhile(callable $predicate): self;

    /**
     * Take N stream elements
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->take(2)->compile()->toArray();
     * => [1, 2]
     * ```
     *
     * @return self<TValue>
     */
    public function take(int $length): self;

    /**
     * Drop N stream elements
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->drop(2)->compile()->toArray();
     * => [3]
     * ```
     *
     * @return self<TValue>
     */
    public function drop(int $length): self;

    /**
     * Call a function for every stream element
     *
     * ```php
     * >>> Stream::emits([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->compile()->toArray();
     * => [2, 3]
     * ```
     *
     * @param callable(TValue): void $callback
     * @return self<TValue>
     */
    public function tap(callable $callback): self;

    /**
     * Emits the specified separator between every pair of elements in the source stream.
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->intersperse(0)->compile()->toArray();
     * => [1, 0, 2, 0, 3]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $separator
     * @return self<TValue|TValueIn>
     */
    public function intersperse(mixed $separator): self;

    /**
     * Writes this stream to the stdout synchronously
     *
     * ```php
     * >>> Stream::emits([1, 2])->lines()->drain();
     * 1
     * 2
     * ```
     *
     * @return self<TValue>
     */
    public function lines(): self;

    /**
     * Deterministically zips elements, terminating when the end of either branch is reached naturally.
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->zip(Stream::emits([4, 5, 6, 7]))->compile()->toArray();
     * => [[1, 4], [2, 5], [3, 6]]
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $that
     * @return self<array{TValue, TValueIn}>
     */
    public function zip(iterable $that): self;

    /**
     * Deterministically interleaves elements, starting on the left, terminating when the end of either branch is reached naturally.
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->interleave(Stream::emits([4, 5, 6, 7]))->compile()->toArray();
     * => [1, 4, 2, 5, 3, 6]
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $that
     * @return self<TValue|TValueIn>
     */
    public function interleave(iterable $that): self;

    /**
     * Produce stream of chunks with given size from this stream
     *
     * ```php
     * >>> Stream::emits([1, 2, 3, 4, 5])->chunks(2);
     * => Stream(Seq(1, 2), Seq(3, 4), Seq(5))
     * ```
     *
     * @param positive-int $size
     * @return self<Seq<TValue>>
     */
    public function chunks(int $size): self;

    /**
     * Partitions the input into a stream of chunks according to a discriminator function.
     *
     * ```php
     * >>> Stream::emits(["Hello", "Hi", "Greetings", "Hey"])
     * >>>     ->groupAdjacentBy(fn($str) => $str[0]);
     * => Stream(
     * =>     ["H", Seq("Hello", "Hi")],
     * =>     ["G", Seq("Greetings")],
     * =>     ["H", Seq("Hey")]
     * => )
     * ```
     *
     * @template TDiscriminator
     * @param callable(TValue): TDiscriminator $discriminator
     * @return self<array{TDiscriminator, Seq<TValue>}>
     */
    public function groupAdjacentBy(callable $discriminator): self;

    /**
     * @template TDiscriminator
     * @param callable(TValue): TDiscriminator $discriminator
     * @return Stream<array{TDiscriminator, Seq<TValue>}>
     */
    public function groupBy(callable $discriminator): self;

    /**
     * Sort streamed elements
     *
     * ```php
     * >>> Stream::emits([2, 1, 3])->sorted(fn($lhs, $rhs) => $lhs - $rhs)->compile()->toArray();
     * => [1, 2, 3]
     *
     * >>> Stream::emits([2, 1, 3])->sorted(fn($lhs, $rhs) => $rhs - $lhs)->compile()->toArray();
     * => [3, 2, 1]
     * ```
     *
     * @param callable(TValue, TValue): int $cmp
     * @return self<TValue>
     */
    public function sorted(callable $cmp): self;

    /**
     * Returns stream unique elements
     *
     * ```php
     * >>> Stream::emits([1, 1, 2])->unique(fn($elem) => $elem)->toArray();
     * => [1, 2]
     * ```
     *
     * @param callable(TValue): array-key $callback returns element unique id
     * @return self<TValue>
     */
    public function unique(callable $callback): self;

    /**
     * ```php
     * >>> Stream::emits([['a', 1], ['b', 2]])->keys()->compile()->toList();
     * => ['a', 'b']
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @psalm-if-this-is StreamChainableOps<array{TKeyIn, TValueIn}>
     * @return self<TKeyIn>
     */
    public function keys(): self;

    /**
     * ```php
     * >>> Stream::emits([['a', 1], ['b', 2]])->values()->compile()->toList();
     * => [1, 2]
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @psalm-if-this-is StreamChainableOps<array{TKeyIn, TValueIn}>
     * @return self<TValueIn>
     */
    public function values(): self;
}
