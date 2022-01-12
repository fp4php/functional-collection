<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class MkStringOperation extends AbstractStreamOperation
{
    public function __invoke(string $start, string $sep, string $end): string
    {
        $interspersed = IntersperseOperation::of($this->gen)($sep);
        $reduced = FoldOperation::of($interspersed)(
            '',
            fn(string $acc, $cur) => $acc . (string) $cur
        );

        return $start . $reduced . $end;
    }
}
