<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Seq;

use Iterator;
use Whsv26\Functional\Collection\Map;
use Whsv26\Functional\Collection\Seq;
use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Operations\AppendedAllOperation;
use Whsv26\Functional\Stream\Operations\AppendedOperation;
use Whsv26\Functional\Stream\Operations\AtOperation;
use Whsv26\Functional\Stream\Operations\CountOperation;
use Whsv26\Functional\Stream\Operations\DropOperation;
use Whsv26\Functional\Stream\Operations\DropWhileOperation;
use Whsv26\Functional\Stream\Operations\EveryMapOperation;
use Whsv26\Functional\Stream\Operations\EveryOfOperation;
use Whsv26\Functional\Stream\Operations\EveryOperation;
use Whsv26\Functional\Stream\Operations\ExistsOfOperation;
use Whsv26\Functional\Stream\Operations\ExistsOperation;
use Whsv26\Functional\Stream\Operations\FilterMapOperation;
use Whsv26\Functional\Stream\Operations\FilterNotNullOperation;
use Whsv26\Functional\Stream\Operations\FilterOfOperation;
use Whsv26\Functional\Stream\Operations\FilterOperation;
use Whsv26\Functional\Stream\Operations\FirstOfOperation;
use Whsv26\Functional\Stream\Operations\FirstOperation;
use Whsv26\Functional\Stream\Operations\FlatMapOperation;
use Whsv26\Functional\Stream\Operations\FoldOperation;
use Whsv26\Functional\Stream\Operations\GroupByOperation;
use Whsv26\Functional\Stream\Operations\IntersperseOperation;
use Whsv26\Functional\Stream\Operations\LastOfOperation;
use Whsv26\Functional\Stream\Operations\LastOperation;
use Whsv26\Functional\Stream\Operations\MapValuesOperation;
use Whsv26\Functional\Stream\Operations\MkStringOperation;
use Whsv26\Functional\Stream\Operations\PrependedAllOperation;
use Whsv26\Functional\Stream\Operations\ReduceOperation;
use Whsv26\Functional\Stream\Operations\SortedOperation;
use Whsv26\Functional\Stream\Operations\TakeOperation;
use Whsv26\Functional\Stream\Operations\TakeWhileOperation;
use Whsv26\Functional\Stream\Operations\TapOperation;
use Whsv26\Functional\Stream\Operations\UniqueOperation;
use Whsv26\Functional\Stream\Operations\ZipOperation;
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
    private ?int $knownSize;

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
     * @psalm-param positive-int $by
     * @psalm-return self<int>
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
     * @psalm-return self<TValue>
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
     * @psalm-param callable(TValue): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return EveryOperation::of($this->getIterator())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param class-string<TValueIn> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return EveryOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param callable(TValue): Option<TValueIn> $callback
     * @return Option<self<TValueIn>>
     */
    public function everyMap(callable $callback): Option
    {
        return EveryMapOperation::of($this->getIterator())($callback)
            ->map(fn($gen) => LinkedList::collect($gen));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return ExistsOperation::of($this->getIterator())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param class-string<TValueIn> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return ExistsOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return Option<TValue>
     */
    public function first(callable $predicate): Option
    {
        return FirstOperation::of($this->getIterator())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param class-string<TValueIn> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TValueIn>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option
    {
        return FirstOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param class-string<TValueIn> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TValueIn>
     */
    public function lastOf(string $fqcn, bool $invariant = false): Option
    {
        return LastOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, TValue): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        return FoldOperation::of($this->getIterator())($init, $callback);
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param callable(TValue|TA, TValue): (TValue|TA) $callback
     * @psalm-return Option<TValue|TA>
     */
    public function reduce(callable $callback): Option
    {
        return ReduceOperation::of($this->getIterator())($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TValue>
     */
    public function head(): Option
    {
        return $this->isCons()
            ? Option::some($this)->map(fn(Cons $cons) => $cons->head)
            : Option::none();
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
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
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return Option<TValue>
     */
    public function last(callable $predicate): Option
    {
        return LastOperation::of($this->getIterator())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TValue>
     */
    public function firstElement(): Option
    {
        return $this->head();
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TValue>
     */
    public function lastElement(): Option
    {
        return LastOperation::of($this->getIterator())();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->knownSize = $this->knownSize
            ?? CountOperation::of($this->getIterator())();
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TValue>
     */
    public function __invoke(int $index): Option
    {
        return $this->at($index);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TValue>
     */
    public function at(int $index): Option
    {
        return AtOperation::of($this->getIterator())($index);
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(TValue): TKO $callback
     * @psalm-return Map<TKO, Seq<TValue>>
     */
    public function groupBy(callable $callback): Map
    {
        return GroupByOperation::of($this->getIterator())($callback);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @psalm-param callable(TValue): TValueIn $callback
     * @psalm-return self<TValueIn>
     */
    public function map(callable $callback): self
    {
        return self::collect(MapValuesOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @psalm-param TValueIn $elem
     * @psalm-return self<TValue|TValueIn>
     */
    public function appended(mixed $elem): self
    {
        return self::collect(AppendedOperation::of($this->getIterator())($elem));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @psalm-param iterable<TValueIn> $suffix
     * @psalm-return self<TValue|TValueIn>
     */
    public function appendedAll(iterable $suffix): self
    {
        return self::collect(AppendedAllOperation::of($this->getIterator())($suffix));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @psalm-param TValueIn $elem
     * @psalm-return self<TValue|TValueIn>
     */
    public function prepended(mixed $elem): self
    {
        return new Cons($elem, $this);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @psalm-param iterable<TValueIn> $prefix
     * @psalm-return self<TValue|TValueIn>
     */
    public function prependedAll(iterable $prefix): self
    {
        return self::collect(PrependedAllOperation::of($this->getIterator())($prefix));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return self<TValue>
     */
    public function filter(callable $predicate): self
    {
        return self::collect(FilterOperation::of($this->getIterator())($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param callable(TValue): Option<TValueIn> $callback
     * @psalm-return self<TValueIn>
     */
    public function filterMap(callable $callback): self
    {
        return self::collect(FilterMapOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
     */
    public function filterNotNull(): self
    {
        return self::collect(FilterNotNullOperation::of($this->getIterator())());
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param class-string<TValueIn> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return self<TValueIn>
     */
    public function filterOf(string $fqcn, bool $invariant = false): self
    {
        return self::collect(FilterOfOperation::of($this->getIterator())($fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param callable(TValue): iterable<TValueIn> $callback
     * @psalm-return self<TValueIn>
     */
    public function flatMap(callable $callback): self
    {
        return self::collect(FlatMapOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return self<TValue>
     */
    public function takeWhile(callable $predicate): self
    {
        return self::collect(TakeWhileOperation::of($this->getIterator())($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return self<TValue>
     */
    public function dropWhile(callable $predicate): self
    {
        return self::collect(DropWhileOperation::of($this->getIterator())($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
     */
    public function take(int $length): self
    {
        return self::collect(TakeOperation::of($this->getIterator())($length));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
     */
    public function drop(int $length): self
    {
        return self::collect(DropOperation::of($this->getIterator())($length));
    }

    /**
     * @inheritDoc
     * @param callable(TValue): void $callback
     * @psalm-return self<TValue>
     */
    public function tap(callable $callback): self
    {
        Stream::emits(TapOperation::of($this->getIterator())($callback))
            ->compile()
            ->drain();

        return $this;
    }

    /**
     * @inheritDoc
     * @experimental
     * @psalm-param callable(TValue): (int|string) $callback
     * @psalm-return self<TValue>
     */
    public function unique(callable $callback): self
    {
        return self::collect(UniqueOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue, TValue): int $cmp
     * @psalm-return self<TValue>
     */
    public function sorted(callable $cmp): self
    {
        return self::collect(SortedOperation::of($this->getIterator())($cmp));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $separator
     * @psalm-return self<TValue|TValueIn>
     */
    public function intersperse(mixed $separator): self
    {
        return self::collect(IntersperseOperation::of($this->getIterator())($separator));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $that
     * @return self<array{TValue, TValueIn}>
     */
    public function zip(iterable $that): self
    {
        return self::collect(ZipOperation::of($this->getIterator())($that));
    }

    /**
     * @inheritDoc
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return MkStringOperation::of($this->getIterator())($start, $sep, $end);
    }
}
