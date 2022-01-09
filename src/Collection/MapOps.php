<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @extends MapChainableOps<TK, TV>
 * @extends MapTerminalOps<TK, TV>
 * @extends MapCastableOps<TK, TV>
 */
interface MapOps extends MapChainableOps, MapTerminalOps, MapCastableOps
{

}
