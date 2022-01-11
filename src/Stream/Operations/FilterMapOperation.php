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
class FilterMapOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TValueIn
     * @psalm-param callable(TValue, TKey): Option<TValueIn> $f
     * @return Generator<TKey, TValueIn>
     */
    public function __invoke(callable $f): Generator
    {
        return (function () use ($f) {
            foreach ($this->gen as $key => $value) {
                $res = $f($value, $key);

                if ($res->isSome()) {
                    yield $key => $res->get();
                }
            }
        })();
    }
}
