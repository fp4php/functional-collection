<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptySeqChainableOps<TV>
 * @extends NonEmptySeqTerminalOps<TV>
 * @extends NonEmptySeqCastableOps<TV>
 */
interface NonEmptySeqOps extends NonEmptySeqChainableOps, NonEmptySeqTerminalOps, NonEmptySeqCastableOps
{

}
