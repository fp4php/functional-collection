<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class AbstractOperation
{
    /**
     * @var Generator<TKey, TValue>
     */
    protected Generator $gen;

    /**
     *
     * @param iterable<TKey, TValue> $gen
     */
    final public function __construct(iterable $gen)
    {
        $this->gen = $gen instanceof Generator
            ? $gen
            : (function () use ($gen) {
                foreach ($gen as $key => $value) {
                    yield $key => $value;
                }
            })();
    }

    /**
     * @psalm-pure
     * @template TKeyIn
     * @template TValueIn
     * @param iterable<TKeyIn, TValueIn> $input
     * @return static<TKeyIn, TValueIn>
     */
    public static function of(iterable $input): static
    {
        return new static($input);
    }
}
