<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class ExistsOfOperation extends AbstractStreamOperation
{
    /**
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn
     * @param bool $invariant
     * @return bool
     */
    public function __invoke(string $fqcn, bool $invariant = false): bool
    {
        return ExistsOperation::of($this->gen)(function ($obj) use ($invariant, $fqcn) {
            return is_object($obj) && ($invariant
                    ? $obj::class === $fqcn
                    : is_a($obj, $fqcn)
                );
        });
    }
}
