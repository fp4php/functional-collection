<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class FoldOperation extends AbstractOperation
{
    /**
     * @template TA
     * @psalm-param TA $init
     * @psalm-param callable(TA, TValue, TKey): TA $f
     * @psalm-return TA
     */
    public function __invoke(mixed $init, callable $f): mixed
    {
        $acc = $init;

        foreach ($this->gen as $key => $value) {
            $acc = $f($acc, $value, $key);
        }

        return $acc;
    }
}
