<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends NonEmptySetChainableOps<TValue>
 * @extends NonEmptySetTerminalOps<TValue>
 * @extends NonEmptySetCastableOps<TValue>
 */
interface NonEmptySetOps extends NonEmptySetChainableOps, NonEmptySetTerminalOps, NonEmptySetCastableOps
{

}
