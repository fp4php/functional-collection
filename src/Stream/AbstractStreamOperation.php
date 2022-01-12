<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream;

use Generator;

/**
 * @template TValue
 * @psalm-immutable
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class AbstractStreamOperation
{
    /**
     * @param Generator<int, TValue> $gen
     */
    final public function __construct(
        protected Generator $gen
    ) { }

    /**
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
