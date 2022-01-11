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
class AppendedAllOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TValueIn
     * @psalm-param iterable<TValueIn> $suffix
     * @return Generator<TValue|TValueIn>
     */
    public function __invoke(iterable $suffix): Generator
    {
        return (function () use ($suffix) {
            foreach ($this->gen as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($suffix as $suffixElem) {
                yield $suffixElem;
            }
        })();
    }
}
