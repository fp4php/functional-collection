<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptySetChainableOps<TV>
 * @extends NonEmptySetTerminalOps<TV>
 * @extends NonEmptySetCastableOps<TV>
 */
interface NonEmptySetOps extends NonEmptySetChainableOps, NonEmptySetTerminalOps, NonEmptySetCastableOps
{

}
