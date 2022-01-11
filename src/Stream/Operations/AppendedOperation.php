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
class AppendedOperation extends AbstractOperation
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
            foreach ($this->gen as $prefixElem) {
                yield $prefixElem;
            }

            yield $elem;
        })();
    }
}
