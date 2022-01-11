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
class EveryMapOperation extends AbstractOperation
{
    /**
     * @template TValueIn
     *
     * @param callable(TValue): Option<TValueIn> $f
     * @return Option<Generator<TValueIn>>
     */
    public function __invoke(callable $f): Option
    {
        $collection = [];

        foreach ($this->gen as $value) {
            $mapped = $f($value);

            if ($mapped->isNone()) {
                return Option::none();
            }

            $collection[] = $mapped->get();
        }

        return Option::some((function() use ($collection) {
            foreach ($collection as $value) {
                yield $value;
            }
        })());
    }
}
