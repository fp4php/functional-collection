<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface SeqCastableOps
{
    /**
     * ```php
     * >>> ArrayList::collect([1, 2])->toList();
     * => [1, 2]
     * ```
     *
     * @return list<TValue>
     */
    public function toList(): array;
}
