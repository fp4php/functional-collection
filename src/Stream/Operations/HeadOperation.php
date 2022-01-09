<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class HeadOperation extends AbstractOperation
{
    /**
     * @return Option<TV>
     */
    public function __invoke(): Option
    {
        return FirstOperation::of($this->gen)();
    }
}
