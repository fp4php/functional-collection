<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TValue
 * @psalm-immutable
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class AbstractOperation
{
    /**
     * @param Generator<int, TValue> $gen
     */
    final public function __construct(
        protected Generator $gen
    ) { }

    /**
     * @psalm-pure
     * @template TKeyIn
     * @template TValueIn
     * @param Generator<int, TValueIn> $input
     * @return static<TValueIn>
     */
    public static function of(Generator $input): static
    {
        return new static($input);
    }
}
