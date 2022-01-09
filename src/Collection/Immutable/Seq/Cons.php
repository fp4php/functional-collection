<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Immutable\Seq;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends LinkedList<TV>
 */
final class Cons extends LinkedList
{
    /**
     * @param TV $head
     * @param LinkedList<TV> $tail
     */
    public function __construct(
        public mixed $head,
        public LinkedList $tail
    ) { }
}
