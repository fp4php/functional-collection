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
class FilterOperation extends AbstractStreamOperation
{
    /**
     * @param callable(TValue): bool $f
     * @return Generator<TValue>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $key => $value) {
                if ($f($value)) {
                    yield $key => $value;
                }
            }
        })();
    }
}
