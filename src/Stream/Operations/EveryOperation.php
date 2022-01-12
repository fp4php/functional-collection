<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class EveryOperation extends AbstractStreamOperation
{
    /**
     * @param callable(TValue): bool $f
     * @return bool
     */
    public function __invoke(callable $f): bool
    {
        $res = true;

        foreach ($this->gen as $value) {
            if (!$f($value)) {
                $res = false;
                break;
            }
        }

        return $res;
    }
}
