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
class IntersperseOperation extends AbstractStreamOperation
{
    /**
     * @template TValueIn
     * @param TValueIn $separator
     * @return Generator<int, TValue|TValueIn>
     */
    public function __invoke(mixed $separator): Generator
    {
        return (function () use ($separator) {
            $isFirst = true;

            foreach ($this->gen as $elem) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    yield $separator;
                }

                yield $elem;
            }
        })();
    }
}
