<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template TKey
 * @template-covariant TValue
 */
interface MapCollector
{
    /**
     * ```php
     * >>> HashMap::collect(['a' =>  1, 'b' => 2])->toArray();
     * => ['a' =>  1, 'b' => 2]
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @param iterable<TKeyIn, TValueIn> $source
     * @return self<TKeyIn, TValueIn>
     */
    public static function collect(iterable $source): self;

    /**
     * ```php
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->toArray();
     * => ['a' =>  1, 'b' => 2]
     * ```
     *
     * @template TKeyIn
     * @template TValueIn
     * @param iterable<array{TKeyIn, TValueIn}> $source
     * @return self<TKeyIn, TValueIn>
     */
    public static function collectPairs(iterable $source): self;
}
