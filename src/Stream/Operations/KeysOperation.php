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
class KeysOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @return Generator<int, TKey>
     */
    public function __invoke(): Generator
    {
        return (function () {
            foreach ($this->gen as $key => $value) {
                yield $key;
            }
        })();
    }
}
