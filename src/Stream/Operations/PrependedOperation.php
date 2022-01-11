<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class PrependedOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TValueIn
     * @psalm-param TValueIn $elem
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
