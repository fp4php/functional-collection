<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class DropWhileOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TKeyOut
     * @psalm-param callable(TValue): bool $f
     * @return Generator<TValue>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            $toggle = true;

            foreach ($this->gen as $value) {
                if (!($toggle = $toggle && $f($value))) {
                    yield $value;
                }
            }
        })();
    }
}
