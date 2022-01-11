<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;
use Generator;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class FilterMapOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TValueIn
     * @psalm-param callable(TValue): Option<TValueIn> $f
     * @return Generator<TValueIn>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $value) {
                $res = $f($value);

                if ($res->isSome()) {
                    yield $res->get();
                }
            }
        })();
    }
}
