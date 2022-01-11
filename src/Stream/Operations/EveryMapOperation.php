<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;
use Generator;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class EveryMapOperation extends AbstractOperation
{
    /**
     * @template TValueIn
     *
     * @param callable(TValue, TKey): Option<TValueIn> $f
     * @return Option<Generator<TKey, TValueIn>>
     */
    public function __invoke(callable $f): Option
    {
        $collection = [];

        foreach ($this->gen as $key => $value) {
            $mapped = $f($value, $key);

            if ($mapped->isNone()) {
                return Option::none();
            }

            $collection[] = [$key, $mapped->get()];
        }

        return Option::some((function() use ($collection) {
            foreach ($collection as [$key, $value]) {
                yield $key => $value;
            }
        })());
    }
}
