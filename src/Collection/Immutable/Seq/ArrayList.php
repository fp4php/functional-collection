<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Immutable\Seq;

use ArrayIterator;
use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Operations\AppendedAllOperation;
use Whsv26\Functional\Stream\Operations\AppendedOperation;
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
use Whsv26\Functional\Stream\Operations\PrependedOperation;
use Whsv26\Functional\Stream\Operations\ReduceOperation;
use Whsv26\Functional\Stream\Operations\SortedOperation;
use Whsv26\Functional\Stream\Operations\TailOperation;
use Whsv26\Functional\Stream\Operations\TakeOperation;
use Whsv26\Functional\Stream\Operations\TakeWhileOperation;
use Whsv26\Functional\Stream\Operations\TapOperation;
use Whsv26\Functional\Stream\Operations\UniqueOperation;
use Whsv26\Functional\Stream\Operations\ZipOperation;
use Whsv26\Functional\Stream\Stream;
use Iterator;
use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyArrayList;
use Whsv26\Functional\Collection\Immutable\Set\HashSet;
use Whsv26\Functional\Collection\Map;
use Whsv26\Functional\Collection\Seq;

/**
 * O(1) {@see Seq::at()} and {@see Seq::__invoke} operations
 *
 * @psalm-immutable
 * @template-covariant TV
 * @implements Seq<TV>
 */
final class ArrayList implements Seq
{
    /**
     * @param list<TV> $elements
     */
    public function __construct(public array $elements) { }

    /**
     * @inheritDoc
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self
    {
        $buffer = [];

        foreach ($source as $elem) {
            $buffer[] = $elem;
        }

        return new self($buffer);
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param TVI $val
     * @return self<TVI>
     */
    public static function singleton(mixed $val): self
    {
        return new self([$val]);
    }

    /**
     * @inheritDoc
     * @return self<empty>
     */
    public static function empty(): self
    {
        return new self([]);
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
            ->toArrayList();
    }

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * @inheritDoc
     * @return list<TV>
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @inheritDoc
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return LinkedList::collect($this);
    }

    /**
     * @inheritDoc
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @return Option<NonEmptyArrayList<TV>>
     */
    public function toNonEmptyArrayList(): Option
    {
        $that = $this;
        return Option::when(
            $this->isNonEmpty(),
            fn() => new NonEmptyArrayList($that)
        );
    }

    /**
     * @inheritDoc
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this);
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap
    {
        return HashMap::collectPairs((function () use ($callback) {
            foreach ($this as $elem) {
                yield $callback($elem);
            }
        })());
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function __invoke(int $index): Option
    {
        return $this->at($index);
    }

    /**
     * O(1) time/space complexity
     *
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option
    {
        return Option::fromNullable($this->elements[$index] ?? null);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function head(): Option
    {
        return Option::fromNullable($this->elements[0] ?? null);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function tail(): self
    {
        return self::collect(TailOperation::of($this->getIterator())());
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function reverse(): self
    {
        return new self(array_reverse($this->elements));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return EveryOperation::of($this->getIterator())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return EveryOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(TV): Option<TVO> $callback
     * @return Option<self<TVO>>
     */
    public function everyMap(callable $callback): Option
    {
        return EveryMapOperation::of($this->getIterator())($callback)
            ->map(fn($gen) => ArrayList::collect($gen));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return ExistsOperation::of($this->getIterator())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return ExistsOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option
    {
        return FirstOperation::of($this->getIterator())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option
    {
        return FirstOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function lastOf(string $fqcn, bool $invariant = false): Option
    {
        return LastOfOperation::of($this->getIterator())($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option
    {
        return LastOperation::of($this->getIterator())($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function firstElement(): Option
    {
        return $this->head();
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function lastElement(): Option
    {
        // TODO known size optimization
        return LastOperation::of($this->getIterator())();
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, TV): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        return FoldOperation::of($this->getIterator())($init, $callback);
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback
     * @psalm-return Option<TV|TA>
     */
    public function reduce(callable $callback): Option
    {
        return ReduceOperation::of($this->getIterator())($callback);
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(TV): TKO $callback
     * @psalm-return Map<TKO, Seq<TV>>
     */
    public function groupBy(callable $callback): Map
    {
        return GroupByOperation::of($this->getIterator())($callback);
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * @inheritDoc
     * @psalm-assert-if-true non-empty-list<TV> $this->elements
     */
    public function isNonEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::collect(MapValuesOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function appended(mixed $elem): self
    {
        return self::collect(AppendedOperation::of($this->getIterator())($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @psalm-return self<TV|TVI>
     */
    public function appendedAll(iterable $suffix): self
    {
        return self::collect(AppendedAllOperation::of($this->getIterator())($suffix));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function prepended(mixed $elem): self
    {
        return self::collect(PrependedOperation::of($this->getIterator())($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $prefix
     * @psalm-return self<TV|TVI>
     */
    public function prependedAll(iterable $prefix): self
    {
        return self::collect(PrependedAllOperation::of($this->getIterator())($prefix));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function filter(callable $predicate): self
    {
        return self::collect(FilterOperation::of($this->getIterator())($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function filterMap(callable $callback): self
    {
        return self::collect(FilterMapOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function filterNotNull(): self
    {
        return self::collect(FilterNotNullOperation::of($this->getIterator())());
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return self<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): self
    {
        return self::collect(FilterOfOperation::of($this->getIterator())($fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return self<TVO>
     */
    public function flatMap(callable $callback): self
    {
        return self::collect(FlatMapOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function takeWhile(callable $predicate): self
    {
        return self::collect(TakeWhileOperation::of($this->getIterator())($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return self<TV>
     */
    public function dropWhile(callable $predicate): self
    {
        return self::collect(DropWhileOperation::of($this->getIterator())($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function take(int $length): self
    {
        return self::collect(TakeOperation::of($this->getIterator())($length));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function drop(int $length): self
    {
        return self::collect(DropOperation::of($this->getIterator())($length));
    }

    /**
     * @inheritDoc
     * @param callable(TV): void $callback
     * @psalm-return self<TV>
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
     * @psalm-param callable(TV): (int|string) $callback
     * @psalm-return self<TV>
     */
    public function unique(callable $callback): self
    {
        return self::collect(UniqueOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TV): int $cmp
     * @psalm-return self<TV>
     */
    public function sorted(callable $cmp): self
    {
        return self::collect(SortedOperation::of($this->getIterator())($cmp));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param TVI $separator
     * @psalm-return self<TV|TVI>
     */
    public function intersperse(mixed $separator): self
    {
        return self::collect(IntersperseOperation::of($this->getIterator())($separator));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param iterable<TVI> $that
     * @return self<array{TV, TVI}>
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
