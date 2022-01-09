<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @extends NonEmptyMapChainableOps<TK, TV>
 * @extends NonEmptyMapTerminalOps<TK, TV>
 * @extends NonEmptyMapCastableOps<TK, TV>
 */
interface NonEmptyMapOps extends NonEmptyMapChainableOps, NonEmptyMapTerminalOps, NonEmptyMapCastableOps
{

}
