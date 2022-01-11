<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class ExistsOperation extends AbstractOperation
{
    /**
     * @psalm-param callable(TValue): bool $f
     */
    public function __invoke(callable $f): bool
    {
        $exists = false;

        foreach ($this->gen as $value) {
            if ($f($value)) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }
}
