<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Generator;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class RepeatOperation extends AbstractOperation
{
    /**
     * @return Generator<int, TValue>
     */
    public function __invoke(): Generator
    {
        return (function () {
            $buffer = ArrayList::collect($this->gen);

            foreach ($buffer as $elem) {
                yield $elem;
            }

            while(true) {
                foreach ($buffer as $elem) {
                    yield $elem;
                }
            }
        })();
    }
}
