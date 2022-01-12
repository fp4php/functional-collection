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
class HeadOperation extends AbstractStreamOperation
{
    /**
     * @return Option<TValue>
     */
    public function __invoke(): Option
    {
        return FirstOperation::of($this->gen)();
    }
}
