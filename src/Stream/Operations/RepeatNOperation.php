<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Generator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class RepeatNOperation extends AbstractOperation
{
    /**
     * @return Generator<int, TV>
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
