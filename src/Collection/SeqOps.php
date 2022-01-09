<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends SeqChainableOps<TValue>
 * @extends SeqTerminalOps<TValue>
 * @extends SeqCastableOps<TValue>
 */
interface SeqOps extends SeqChainableOps, SeqTerminalOps, SeqCastableOps
{

}
