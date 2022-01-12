<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class FilterMapOperation extends AbstractStreamOperation
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
