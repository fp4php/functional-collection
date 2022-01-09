<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Whsv26\Functional\Core\Option;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class FilterOfOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn
     * @psalm-return Generator<TK, TVO>
     */
    public function __invoke(string $fqcn, bool $invariant = false): Generator
    {
        return (function () use ($fqcn, $invariant) {
            foreach ($this->gen as $key => $value) {
                $option = Option::some($value)->filterOf($fqcn, $invariant);

                if ($option->isSome()) {
                    yield $key => $option->get();
                }
            }
        })();
    }
}
