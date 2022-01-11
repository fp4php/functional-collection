<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class SecondOperation extends AbstractOperation
{
    /**
     * @param null|callable(TValue): bool $f
     * @return Option<TValue>
     */
    public function __invoke(?callable $f = null): Option
    {
        if (is_null($f)) {
            $f = fn(mixed $value): bool => true;
        }

        $i = 0;
        $second = null;

        foreach ($this->gen as $value) {
            if ($f($value) && 1 === $i) {
                $second = $value;
                break;
            }

            if ($f($value)) {
                $i++;
            }
        }

        return Option::fromNullable($second);
    }
}
