<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
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
