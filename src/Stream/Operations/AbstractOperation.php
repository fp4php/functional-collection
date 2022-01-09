<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @psalm-consistent-constructor
 * @psalm-consistent-templates
 */
class AbstractOperation
{
    /**
     * @var Generator<TK, TV>
     */
    protected Generator $gen;

    /**
     *
     * @param iterable<TK, TV> $gen
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
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $input
     * @return static<TKI, TVI>
     */
    public static function of(iterable $input): static
    {
        return new static($input);
    }
}
