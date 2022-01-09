<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class LastOperation extends AbstractOperation
{
    /**
     * @param null|callable(TV, TK): bool $f
     * @return Option<TV>
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
