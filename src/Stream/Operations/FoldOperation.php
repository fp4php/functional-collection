<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class FoldOperation extends AbstractOperation
{
    /**
     * @template TA
     * @psalm-param TA $init
     * @psalm-param callable(TA, TValue): TA $f
     * @psalm-return TA
     */
    public function __invoke(mixed $init, callable $f): mixed
    {
        $acc = $init;

        foreach ($this->gen as $value) {
            $acc = $f($acc, $value);
        }

        return $acc;
    }
}
