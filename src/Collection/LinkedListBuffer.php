<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * Provides constant time append to list
 *
 * @internal
 * @template TValue
 */
final class LinkedListBuffer
{
    /**
     * @var LinkedList<TValue>
     */
    private LinkedList $first;

    /**
     * @var null|Cons<TValue>
     */
    private ?Cons $last;

    private int $length;

    public function __construct()
    {
        $this->flush();
    }

    /**
     * @param TValue $elem
     * @return self<TValue>
     */
    public function append(mixed $elem): self
    {
        $appended = new Cons($elem, Nil::getInstance());

        if (0 === $this->length) {
            $this->first = $appended;
        } elseif (isset($this->last)) {
            /**
             * @psalm-suppress InaccessibleProperty
             */
            $this->last->tail = $appended;
        }

        $this->last = $appended;
        $this->length++;

        return $this;
    }

    /**
     * @return LinkedList<TValue>
     */
    public function toLinkedList(): LinkedList
    {
        $first = $this->first;
        $this->flush();

        return $first;
    }

    private function flush(): void
    {
        $this->first = Nil::getInstance();
        $this->last = null;
        $this->length = 0;
    }
}
