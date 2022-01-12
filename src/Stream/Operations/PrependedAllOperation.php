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
class PrependedAllOperation extends AbstractStreamOperation
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
