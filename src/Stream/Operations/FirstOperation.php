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
class FirstOperation extends AbstractOperation
{
    /**
     * @param null|callable(TV, TK): bool $f
     * @return Option<TV>
     */
    public function __invoke(?callable $f = null): Option
    {
        if (is_null($f)) {
            $f = fn(mixed $value, mixed $key): bool => true;
        }

        $first = null;

        foreach ($this->gen as $key => $value) {
            if ($f($value, $key)) {
                $first = $value;
                break;
            }
        }

        return Option::fromNullable($first);
    }
}
