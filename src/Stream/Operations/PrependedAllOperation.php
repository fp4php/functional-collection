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
class PrependedAllOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TValueIn
     * @psalm-param iterable<TValueIn> $prefix
     * @return Generator<TValue|TValueIn>
     */
    public function __invoke(iterable $prefix): Generator
    {
        return (function () use ($prefix) {
            foreach ($prefix as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($this->gen as $suffixElem) {
                yield $suffixElem;
            }
        })();
    }
}
