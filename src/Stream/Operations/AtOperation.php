<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class AtOperation extends AbstractStreamOperation
{
    /**
     * @param int $pos
     * @return Option<TValue>
     */
    public function __invoke(mixed $pos): Option
    {
        $first = null;

        foreach ($this->gen as $key => $value) {
            if ($key === $pos) {
                $first = $value;
                break;
            }
        }

        return Option::fromNullable($first);
    }
}
