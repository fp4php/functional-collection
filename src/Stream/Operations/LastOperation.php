<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class LastOperation extends AbstractOperation
{
    /**
     * @param null|callable(TValue, TKey): bool $f
     * @return Option<TValue>
     */
    public function __invoke(?callable $f = null): Option
    {
        $last = null;

        foreach ($this->gen as $key => $value) {
            if (is_null($f) || $f($value, $key)) {
                $last = $value;
            }
        }

        return Option::fromNullable($last);
    }
}
