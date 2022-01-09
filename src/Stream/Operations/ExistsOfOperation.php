<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class ExistsOfOperation extends AbstractOperation
{
    /**
     * @template TVO
     * @param class-string<TVO> $fqcn fully qualified class name
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
