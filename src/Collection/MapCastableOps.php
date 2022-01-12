<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @template TKey
 * @template-covariant TValue
 * @psalm-immutable
 */
interface MapCastableOps
{
    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toArray();
     * => ['a' => 1, 'b' => 2]
     * ```
     *
     * @template TKeyIn of array-key
     * @template TValueIn
     * @psalm-if-this-is MapCastableOps<TKeyIn, TValueIn>
     * @return array<TKeyIn, TValueIn>
     */
    public function toArray(): array;
}
