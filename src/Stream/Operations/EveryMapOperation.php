<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;
use Generator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class EveryMapOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param callable(TV, TK): Option<TVO> $f
     * @return Option<Generator<TK, TVO>>
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
