<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Whsv26\Functional\Core\Option;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class FilterOfOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-template TValueIn
     * @psalm-param class-string<TValueIn> $fqcn
     * @psalm-return Generator<TKey, TValueIn>
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
