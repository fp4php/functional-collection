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
class DropWhileOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TKeyOut
     * @psalm-param callable(TValue, TKey): bool $f
     * @return Generator<TKey, TValue>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            $toggle = true;

            foreach ($this->gen as $key => $value) {
                if (!($toggle = $toggle && $f($value, $key))) {
                    yield $key => $value;
                }
            }
        })();
    }
}
