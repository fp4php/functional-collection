<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Stream\Stream;

/**
 * @psalm-immutable
 * @template TKey
 * @template-covariant TValue
 */
interface MapCastableOps
{
    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toStream()->compile()->toList();
     * => [['a', 1], ['b', 2]]
     * ```
     *
     * @return Stream<array{TKey, TValue}>
     */
    public function toStream(): Stream;
}
