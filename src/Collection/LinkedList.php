<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;
use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Stream;

/**
 * O(1) {@see Seq::prepended} operation
 * Fast {@see Seq::reverse} operation
 *
 * @psalm-immutable
 * @template-covariant TValue
 * @implements Seq<TValue>
 */
abstract class LinkedList implements Seq
{
    /**
     * @psalm-allow-private-mutation
     */
    private ?int $knownSize = null;

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $source
     * @return self<TValueIn>
     */
    public static function collect(iterable $source): self
    {
        $buffer = new LinkedListBuffer();

        foreach ($source as $elem) {
            $buffer->append($elem);
        }

        return $buffer->toLinkedList();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $val
     * @return self<TValueIn>
     */
    public static function singleton(mixed $val): self
    {
        return new Cons($val, Nil::getInstance());
    }

    /**
     * @inheritDoc
     * @return self<empty>
     */
    public static function empty(): self
    {
        return Nil::getInstance();
    }

    /**
     * @inheritDoc
     * @param positive-int $by
     * @return self<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): self
    {
        return Stream::range($start, $stopExclusive, $by)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @return Iterator<int, TValue>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this);
    }

    /**
     * @inheritDoc
     * @return Stream<TValue>
     */
    public function stream(): Stream
    {
        return Stream::emits($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return list<TValue>
     */
    public function toList(): array
    {
        return Stream::emits($this->getIterator())
            ->compile()
            ->toList();
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function reverse(): self
    {
        $list = Nil::getInstance();

        foreach ($this as $elem) {
            $list = $list->prepended($elem);
        }

        return $list;
    }

    /**
     * @psalm-assert-if-true Cons<TValue> $this
     */
    public function isCons(): bool
    {
        return $this instanceof Cons;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return !$this->isCons();
    }

    /**
     * @inheritDoc
     */
    public function isNonEmpty(): bool
    {
        return $this->isCons();
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return $this->stream()
            ->compile()
            ->every($predicate);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn
     * @param bool $invariant
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->stream()
            ->compile()
            ->everyOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param callable(TValue): Option<TValueIn> $callback
     * @return Option<self<TValueIn>>
     */
    public function everyMap(callable $callback): Option
    {
        return $this->stream()
            ->compile()
            ->everyMap($callback)
            ->map(fn(Stream $stream) => $stream->compile()->toLinkedList());
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return $this->stream()
            ->compile()
            ->exists($predicate);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn
     * @param bool $invariant
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->stream()
            ->compile()
            ->existsOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     * @return Option<TValue>
     */
    public function first(callable $predicate): Option
    {
        return $this->stream()
            ->compile()
            ->first($predicate);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn
     * @param bool $invariant
     * @return Option<TValueIn>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option
    {
        return $this->stream()
            ->compile()
            ->firstOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn
     * @param bool $invariant
     * @return Option<TValueIn>
     */
    public function lastOf(string $fqcn, bool $invariant = false): Option
    {
        return $this->stream()
            ->compile()
            ->lastOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @template TA
     * @param TA $init initial accumulator value
     * @param callable(TA, TValue): TA $callback (accumulator, current element): new accumulator
     * @return TA
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        return $this->stream()
            ->compile()
            ->fold($init, $callback);
    }

    /**
     * @inheritDoc
     * @template TA
     * @param callable(TValue|TA, TValue): (TValue|TA) $callback
     * @return Option<TValue|TA>
     */
    public function reduce(callable $callback): Option
    {
        return $this->stream()
            ->compile()
            ->reduce($callback);
    }

    /**
     * @inheritDoc
     * @return Option<TValue>
     */
    public function head(): Option
    {
        return $this->isCons()
            ? Option::some($this)->map(fn(Cons $cons) => $cons->head)
            : Option::none();
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function tail(): self
    {
        return match (true) {
            $this instanceof Cons => $this->tail,
            $this instanceof Nil => $this,
        };
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     * @return Option<TValue>
     */
    public function last(callable $predicate): Option
    {
        return $this->stream()
            ->compile()
            ->last($predicate);
    }

    /**
     * @inheritDoc
     * @return Option<TValue>
     */
    public function firstElement(): Option
    {
        return $this->head();
    }

    /**
     * @inheritDoc
     * @return Option<TValue>
     */
    public function lastElement(): Option
    {
        return $this->stream()
            ->compile()
            ->lastElement();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->knownSize = $this->knownSize
            ?? $this->stream()->compile()->count();
    }

    /**
     * @inheritDoc
     * @return Option<TValue>
     */
    public function __invoke(int $index): Option
    {
        return $this->at($index);
    }

    /**
     * @inheritDoc
     * @return Option<TValue>
     */
    public function at(int $index): Option
    {
        return $this->stream()
            ->compile()
            ->at($index);
    }

    /**
     * @inheritDoc
     * @template TKO
     * @param callable(TValue): TKO $callback
     * @return Map<TKO, Seq<TValue>>
     */
    public function groupBy(callable $callback): Map
    {
        return $this->stream()
            ->groupBy($callback)
            ->compile()
            ->toHashMap();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param callable(TValue): TValueIn $callback
     * @return self<TValueIn>
     */
    public function map(callable $callback): self
    {
        return $this->stream()
            ->map($callback)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $elem
     * @return self<TValue|TValueIn>
     */
    public function appended(mixed $elem): self
    {
        return $this->stream()
            ->appended($elem)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $suffix
     * @return self<TValue|TValueIn>
     */
    public function appendedAll(iterable $suffix): self
    {
        return $this->stream()
            ->appendedAll($suffix)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $elem
     * @return self<TValue|TValueIn>
     */
    public function prepended(mixed $elem): self
    {
        return new Cons($elem, $this);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $prefix
     * @return self<TValue|TValueIn>
     */
    public function prependedAll(iterable $prefix): self
    {
        return $this->stream()
            ->prependedAll($prefix)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     * @return self<TValue>
     */
    public function filter(callable $predicate): self
    {
        return $this->stream()
            ->filter($predicate)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param callable(TValue): Option<TValueIn> $callback
     * @return self<TValueIn>
     */
    public function filterMap(callable $callback): self
    {
        return $this->stream()
            ->filterMap($callback)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function filterNotNull(): self
    {
        return $this->stream()
            ->filterNotNull()
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param class-string<TValueIn> $fqcn
     * @param bool $invariant
     * @return self<TValueIn>
     */
    public function filterOf(string $fqcn, bool $invariant = false): self
    {
        return $this->stream()
            ->filterOf($fqcn, $invariant)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param callable(TValue): iterable<TValueIn> $callback
     * @return self<TValueIn>
     */
    public function flatMap(callable $callback): self
    {
        return $this->stream()
            ->flatMap($callback)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     * @return self<TValue>
     */
    public function takeWhile(callable $predicate): self
    {
        return $this->stream()
            ->takeWhile($predicate)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     * @return self<TValue>
     */
    public function dropWhile(callable $predicate): self
    {
        return $this->stream()
            ->dropWhile($predicate)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function take(int $length): self
    {
        return $this->stream()
            ->take($length)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function drop(int $length): self
    {
        return $this->stream()
            ->drop($length)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @param callable(TValue): void $callback
     * @return self<TValue>
     */
    public function tap(callable $callback): self
    {
        $this->stream()
            ->tap($callback)
            ->compile()
            ->drain();

        return $this;
    }

    /**
     * @inheritDoc
     * @param callable(TValue): array-key $callback
     * @return self<TValue>
     */
    public function unique(callable $callback): self
    {
        return $this->stream()
            ->unique($callback)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @param callable(TValue, TValue): int $cmp
     * @return self<TValue>
     */
    public function sorted(callable $cmp): self
    {
        return $this->stream()
            ->sorted($cmp)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $separator
     * @return self<TValue|TValueIn>
     */
    public function intersperse(mixed $separator): self
    {
        return $this->stream()
            ->intersperse($separator)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $that
     * @return self<array{TValue, TValueIn}>
     */
    public function zip(iterable $that): self
    {
        return $this->stream()
            ->zip($that)
            ->compile()
            ->toLinkedList();
    }

    /**
     * @inheritDoc
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return $this->stream()
            ->compile()
            ->mkString($start, $sep, $end);
    }
}
