<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class PrependedOperation extends AbstractStreamOperation
{
    /**
     * @template TValueIn
     * @param TValueIn $elem
     * @return Generator<TValue|TValueIn>
     */
    public function __invoke(mixed $elem): Generator
    {
        return (function () use ($elem) {
            yield $elem;

            foreach ($this->gen as $suffixElem) {
                yield $suffixElem;
            }
        })();
    }
}
