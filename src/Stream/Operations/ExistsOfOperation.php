<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class ExistsOfOperation extends AbstractOperation
{
    /**
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
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
