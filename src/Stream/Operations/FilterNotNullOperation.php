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
class FilterNotNullOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @return Generator<TKey, TValue>
     */
    public function __invoke(): Generator
    {
        return (function () {
            foreach ($this->gen as $key => $value) {
                if (null !== $value) {
                    yield $key => $value;
                }
            }
        })();
    }
}
