<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class HeadOperation extends AbstractOperation
{
    /**
     * @return Option<TValue>
     */
    public function __invoke(): Option
    {
        return FirstOperation::of($this->gen)();
    }
}
