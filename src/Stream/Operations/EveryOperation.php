<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class EveryOperation extends AbstractOperation
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
