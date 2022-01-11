<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class ExistsOperation extends AbstractOperation
{
    /**
     * @psalm-param callable(TValue, TKey): bool $f
     */
    public function __invoke(callable $f): bool
    {
        $exists = false;

        foreach ($this->gen as $key => $value) {
            if ($f($value, $key)) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }
}
