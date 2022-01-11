<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class ReduceOperation extends AbstractOperation
{
    /**
     * @template TA
     * @psalm-param callable(TValue|TA, TValue): (TValue|TA) $f
     * @psalm-return Option<TValue|TA>
     */
    public function __invoke(callable $f): Option
    {
        /** @var TValue|TA $acc */
        $acc = null;
        $toggle = true;

        foreach ($this->gen as $value) {
            if ($toggle) {
                $acc = $value;
                $toggle = false;
                continue;
            }

            $acc = $f($acc, $value);
        }

        return Option::fromNullable($acc);
    }
}
