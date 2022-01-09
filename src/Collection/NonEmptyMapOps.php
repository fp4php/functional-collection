<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @template TKey
 * @template-covariant TValue
 * @psalm-immutable
 * @extends NonEmptyMapChainableOps<TKey, TValue>
 * @extends NonEmptyMapTerminalOps<TKey, TValue>
 * @extends NonEmptyMapCastableOps<TKey, TValue>
 */
interface NonEmptyMapOps extends NonEmptyMapChainableOps, NonEmptyMapTerminalOps, NonEmptyMapCastableOps
{

}
