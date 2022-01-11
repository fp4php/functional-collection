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
     * @var Generator<int, TValue>
     */
    protected Generator $gen;

    /**
     *
     * @param iterable<TValue> $gen
     */
    final public function __construct(iterable $gen)
    {
        $this->gen = $gen instanceof Generator
            ? $gen
            : (function () use ($gen) {
                foreach ($gen as $value) {
                    yield $value;
                }
            })();
    }

    /**
     * @psalm-pure
     * @template TKeyIn
     * @template TValueIn
     * @param iterable<int, TValueIn> $input
     * @return static<TValueIn>
     */
    public static function of(iterable $input): static
    {
        return new static($input);
    }
}
