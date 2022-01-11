<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Immutable\Seq;

/**
 * @psalm-immutable
 * @template-covariant TValue
 * @extends LinkedList<TValue>
 */
final class Cons extends LinkedList
{
    /**
     * @param TValue $head
     * @param LinkedList<TValue> $tail
     */
    public function __construct(
        public mixed $head,
        public LinkedList $tail
    ) { }
}
