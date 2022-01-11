<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class CountOperation extends AbstractOperation
{
    public function __invoke(): int
    {
        $counter = 0;

        foreach ($this->gen as $ignored) {
            $counter++;
        }

        return $counter;
    }
}
