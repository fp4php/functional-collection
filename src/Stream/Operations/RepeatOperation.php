<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Whsv26\Functional\Collection\ArrayList;
use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class RepeatOperation extends AbstractStreamOperation
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
