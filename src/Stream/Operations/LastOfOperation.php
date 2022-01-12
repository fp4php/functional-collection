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
class LastOfOperation extends AbstractStreamOperation
{
    /**
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn
     * @param bool $invariant
     * @return Option<TValueIn>
     */
    public function __invoke(string $fqcn, bool $invariant = false): Option
    {
        $last = LastOperation::of($this->gen)(function ($obj) use ($invariant, $fqcn) {
            return is_object($obj) && ($invariant
                    ? $obj::class === $fqcn
                    : is_a($obj, $fqcn)
                );
        });

        return $last->filterOf($fqcn, $invariant);
    }
}
