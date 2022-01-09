<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends NonEmptySeqChainableOps<TValue>
 * @extends NonEmptySeqTerminalOps<TValue>
 * @extends NonEmptySeqCastableOps<TValue>
 */
interface NonEmptySeqOps extends NonEmptySeqChainableOps, NonEmptySeqTerminalOps, NonEmptySeqCastableOps
{

}
