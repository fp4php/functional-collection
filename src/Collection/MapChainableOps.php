<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @template TKey
 * @template-covariant TValue
 * @psalm-immutable
 */
interface MapChainableOps
{
    /**
     * Produces new collection with given element
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->updated('b', 3)->toArray();
     * => ['a' => 1, 'b' => 3]
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @param TKeyIn $key
     * @param TValueIn $value
     * @return Map<TKey|TKeyIn, TValue|TValueIn>
     */
    public function updated(mixed $key, mixed $value): Map;

    /**
     * Produces new collection without an element with given key
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->removed('b')->toArray();
     * => ['a' => 1]
     * ```
     *
     * @param TKey $key
     * @return Map<TKey, TValue>
     */
    public function removed(mixed $key): Map;

    /**
     * Returns sequence of collection keys
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->keys()->toList();
     * => ['a', 'b']
     * ```
     *
     * @return Seq<TKey>
     */
    public function keys(): Seq;

    /**
     * Returns sequence of collection values
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->values()->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TValue>
     */
    public function values(): Seq;
}
