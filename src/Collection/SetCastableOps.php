<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface SetCastableOps
{
    /**
     * ```php
     * >>> HashSet::collect([1, 2, 2])->toList();
     * => [1, 2]
     * ```
     *
     * @return list<TValue>
     */
    public function toList(): array;
}
