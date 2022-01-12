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
class FilterNotNullOperation extends AbstractStreamOperation
{
    /**
     * @return Generator<TValue>
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
