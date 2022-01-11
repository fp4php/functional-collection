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
class ValuesOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @return Generator<int, TValue>
     */
    public function __invoke(): Generator
    {
        return (function () {
            foreach ($this->gen as $value) {
                yield $value;
            }
        })();
    }
}
