<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class ExistsOperation extends AbstractStreamOperation
{
    /**
     * @param callable(TValue): bool $f
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
