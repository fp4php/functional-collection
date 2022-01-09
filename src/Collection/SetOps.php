<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends SetChainableOps<TValue>
 * @extends SetTerminalOps<TValue>
 * @extends SetCastableOps<TValue>
 */
interface SetOps extends SetChainableOps, SetTerminalOps, SetCastableOps
{

}
