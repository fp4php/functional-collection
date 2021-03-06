<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class ReduceOperation extends AbstractStreamOperation
{
    /**
     * @template TA
     * @param callable(TValue|TA, TValue): (TValue|TA) $f
     * @return Option<TValue|TA>
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
