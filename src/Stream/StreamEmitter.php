<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream;

use Whsv26\Functional\Core\Unit;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface StreamEmitter
{
    /**
     * Create singleton stream with one element
     *
     * ```php
     * >>> Stream::emit(1)->compile()->toArray();
     * => [1]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $elem
     * @return Stream<TValueIn>
     */
    public static function emit(mixed $elem): Stream;

    /**
     * Emits elements from iterable source
     *
     * ```php
     * >>> Stream::emits([1, 2])->compile()->toArray();
     * => [1, 2]
     * ```
     *
     * @template TValueIn
     * @param iterable<TValueIn> $source
     * @return Stream<TValueIn>
     */
    public static function emits(iterable $source): Stream;

    /**
     * Repeat this stream an infinite number of times.
     *
     * ```php
     * >>> Stream::emits([1,2,3])->repeat()->take(8)->compile()->toArray();
     * => [1, 2, 3, 1, 2, 3, 1, 2]
     * ```
     *
     * @return Stream<TValue>
     */
    public function repeat(): Stream;

    /**
     * Repeat this stream N times
     *
     * ```php
     * >>> Stream::emit(1)->repeatN(3)->compile()->toArray();
     * => [1, 1, 1]
     * ```
     *
     * @return Stream<TValue>
     */
    public function repeatN(int $times): Stream;

    /**
     * Creates an infinite stream that always returns the supplied value
     *
     * ```php
     * >>> Stream::constant(0)->take(3)->compile()->toArray();
     * => [0, 0, 0]
     * ```
     *
     * @template TValueIn
     * @param TValueIn $const
     * @return Stream<TValueIn>
     */
    public static function constant(mixed $const): Stream;

    /**
     * Creates int stream of given range
     *
     * ```php
     * >>> Stream::range(0, 10, 2)->compile()->toArray();
     * => [0, 2, 4, 6, 8]
     * ```
     *
     * @psalm-param positive-int $by
     * @psalm-return Stream<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): Stream;

    /**
     * Creates an infinite stream
     *
     * ```php
     * >>> Stream::infinite()->map(fn() => rand(0, 1))->take(2)->compile()->toArray();
     * => [0, 1]
     * ```
     *
     * @return Stream<Unit>
     */
    public static function infinite(): Stream;
}
