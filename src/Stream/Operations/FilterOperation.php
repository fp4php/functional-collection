<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class FilterOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-param callable(TValue): bool $f
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
