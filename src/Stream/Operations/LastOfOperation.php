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
class LastOfOperation extends AbstractOperation
{
    /**
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
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
