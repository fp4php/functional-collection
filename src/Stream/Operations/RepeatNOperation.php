<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Whsv26\Functional\Collection\Seq\ArrayList;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class RepeatNOperation extends AbstractOperation
{
    /**
     * @return Generator<int, TValue>
     */
    public function __invoke(int $times): Generator
    {
        return (function () use ($times) {
            $buffer = ArrayList::collect($this->gen);

            foreach ($buffer as $elem) {
                yield $elem;
            }

            for($i = 0; $i < $times - 1; $i++) {
                foreach ($buffer as $elem) {
                    yield $elem;
                }
            }
        })();
    }
}
