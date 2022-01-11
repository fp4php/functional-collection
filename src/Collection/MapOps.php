<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @template TKey
 * @template-covariant TValue
 * @psalm-immutable
 * @extends MapChainableOps<TKey, TValue>
 * @extends MapTerminalOps<TKey, TValue>
 */
interface MapOps extends MapChainableOps, MapTerminalOps
{

}
