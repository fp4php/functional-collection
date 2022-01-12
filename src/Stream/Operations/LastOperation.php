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
class LastOperation extends AbstractStreamOperation
{
    /**
     * @param null|callable(TValue): bool $f
     * @return Option<TValue>
     */
    public function __invoke(?callable $f = null): Option
    {
        $last = null;

        foreach ($this->gen as $value) {
            if (is_null($f) || $f($value)) {
                $last = $value;
            }
        }

        return Option::fromNullable($last);
    }
}
