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
class FilterOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-param callable(TValue, TKey): bool $f
     * @return Generator<TKey, TValue>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $key => $value) {
                if ($f($value, $key)) {
                    yield $key => $value;
                }
            }
        })();
    }
}
