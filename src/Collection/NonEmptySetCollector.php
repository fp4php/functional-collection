<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;

/**
 * @psalm-immutable
 * @template-covariant TValue
 */
interface NonEmptySetCollector
{
    /**
     * @template TValueIn
     * @param iterable<TValueIn> $source
     * @return Option<self<TValueIn>>
     */
    public static function collect(iterable $source): Option;

    /**
     * @template TValueIn
     * @param iterable<TValueIn> $source
     * @return self<TValueIn>
     */
    public static function collectUnsafe(iterable $source): self;

    /**
     * @template TValueIn
     * @param non-empty-array<TValueIn>|NonEmptyCollection<TValueIn> $source
     * @return self<TValueIn>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): self;

}
