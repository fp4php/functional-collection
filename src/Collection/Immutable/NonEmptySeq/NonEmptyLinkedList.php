<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Immutable\NonEmptySeq;

use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Operations\AppendedAllOperation;
use Whsv26\Functional\Stream\Operations\AppendedOperation;
use Whsv26\Functional\Stream\Operations\AtOperation;
use Whsv26\Functional\Stream\Operations\EveryMapOperation;
use Whsv26\Functional\Stream\Operations\EveryOfOperation;
use Whsv26\Functional\Stream\Operations\EveryOperation;
use Whsv26\Functional\Stream\Operations\ExistsOfOperation;
use Whsv26\Functional\Stream\Operations\ExistsOperation;
use Whsv26\Functional\Stream\Operations\FirstOfOperation;
use Whsv26\Functional\Stream\Operations\FirstOperation;
use Whsv26\Functional\Stream\Operations\GroupByOperation;
use Whsv26\Functional\Stream\Operations\LastOfOperation;
use Whsv26\Functional\Stream\Operations\LastOperation;
use Whsv26\Functional\Stream\Operations\MapValuesOperation;
use Whsv26\Functional\Stream\Operations\PrependedAllOperation;
use Whsv26\Functional\Stream\Operations\PrependedOperation;
use Whsv26\Functional\Stream\Operations\ReduceOperation;
use Whsv26\Functional\Stream\Operations\SortedOperation;
use Whsv26\Functional\Stream\Operations\TapOperation;
use Whsv26\Functional\Stream\Operations\UniqueOperation;
use Whsv26\Functional\Stream\Stream;
use Iterator;
use Whsv26\Functional\Collection\Immutable\Map\Entry;
use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Immutable\NonEmptyMap\NonEmptyHashMap;
use Whsv26\Functional\Collection\Immutable\NonEmptySet\NonEmptyHashSet;
use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Immutable\Seq\Cons;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\Immutable\Set\HashSet;
use Whsv26\Functional\Collection\Mutable\LinkedListIterator;
use Whsv26\Functional\Collection\NonEmptyCollection;
use Whsv26\Functional\Collection\NonEmptyMap;
use Whsv26\Functional\Collection\NonEmptySeq;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements NonEmptySeq<TV>
 */
final class NonEmptyLinkedList implements NonEmptySeq
{
    /**
     * @param TV $head
     * @param LinkedList<TV> $tail
     */
    public function __construct(
        public mixed $head,
        public LinkedList $tail
    ) { }

    /**
     * @inheritDoc
     * @template TVI
     * @param iterable<TVI> $source
     * @return Option<self<TVI>>
     */
    public static function collect(iterable $source): Option
    {
        return Option::some(LinkedList::collect($source))
            ->filter(fn($list) => $list instanceof Cons)
            ->map(fn(Cons $cons) => new NonEmptyLinkedList($cons->head, $cons->tail));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collectUnsafe(iterable $source): self
    {
        return self::collect($source)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param non-empty-array<TVI>|NonEmptyCollection<TVI> $source
     * @return self<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): self
    {
        return self::collectUnsafe($source);
    }

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this->toLinkedList());
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->tail->count() + 1;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return LinkedList<TV>
     */
    public function filter(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->filter($predicate);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(TV): Option<TVO> $callback
     * @return LinkedList<TVO>
     */
    public function filterMap(callable $callback): LinkedList
    {
        return $this->toLinkedList()->filterMap($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    public function filterNotNull(): LinkedList
    {
        return $this->toLinkedList()->filterNotNull();
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return LinkedList<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): LinkedList
    {
        return $this->toLinkedList()->filterOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return LinkedList<TVO>
     */
    public function flatMap(callable $callback): LinkedList
    {
        return $this->toLinkedList()->flatMap($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function reverse(): self
    {
        return self::collectUnsafe($this->toLinkedList()->reverse());
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    public function tail(): LinkedList
    {
        return $this->tail;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return LinkedList<TV>
     */
    public function takeWhile(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->takeWhile($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return LinkedList<TV>
     */
    public function dropWhile(callable $predicate): LinkedList
    {
        return $this->toLinkedList()->dropWhile($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    public function take(int $length): LinkedList
    {
        return $this->toLinkedList()->take($length);
    }

    /**
     * @inheritDoc
     * @psalm-return LinkedList<TV>
     */
    public function drop(int $length): LinkedList
    {
        return $this->toLinkedList()->drop($length);
    }

    /**
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::collectUnsafe(MapValuesOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function appended(mixed $elem): self
    {
        return self::collectUnsafe(AppendedOperation::of($this->getIterator())($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @psalm-return self<TV|TVI>
     */
    public function appendedAll(iterable $suffix): self
    {
        return self::collectUnsafe(AppendedAllOperation::of($this->getIterator())($suffix));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function prepended(mixed $elem): self
    {
        return self::collectUnsafe(PrependedOperation::of($this->getIterator())($elem));
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param iterable<TVI> $prefix
     * @psalm-return self<TV|TVI>
     */
    public function prependedAll(iterable $prefix): self
    {
        return self::collectUnsafe(PrependedAllOperation::of($this->getIterator())($prefix));
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
        return self::collectUnsafe(UniqueOperation::of($this->getIterator())($callback));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV, TV): int $cmp
     * @psalm-return self<TV>
     */
    public function sorted(callable $cmp): self
    {
        return self::collectUnsafe(SortedOperation::of($this->getIterator())($cmp));
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
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option
    {
        return AtOperation::of($this->getIterator())($index);
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
     * @psalm-param class-string<TVO> $fqcn
     * @psalm-param bool $invariant
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
            ->map(fn($gen) => NonEmptyLinkedList::collectUnsafe($gen));
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
     * @psalm-param class-string<TVO> $fqcn
     * @psalm-param bool $invariant
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
     * @psalm-param class-string<TVO> $fqcn
     * @psalm-param bool $invariant
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
     * @psalm-return TV
     */
    public function head(): mixed
    {
        return $this->head;
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
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback
     * @psalm-return (TV|TA)
     */
    public function reduce(callable $callback): mixed
    {
        return ReduceOperation::of($this->getIterator())($callback)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function firstElement(): mixed
    {
        return $this->head();
    }

    /**
     * @inheritDoc
     * @psalm-return TV
     */
    public function lastElement(): mixed
    {
        return LastOperation::of($this->getIterator())()->getUnsafe();
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(TV): TKO $callback
     * @psalm-return NonEmptyMap<TKO, NonEmptySeq<TV>>
     */
    public function groupBy(callable $callback): NonEmptyMap
    {
        $grouped = GroupByOperation::of($this)($callback);

        /**
         * @var NonEmptyMap<TKO, Cons<TV>> $nonEmptyGrouped
         */
        $nonEmptyGrouped = new NonEmptyHashMap($grouped);

        return $nonEmptyGrouped->mapValues(fn(Entry $entry) => new NonEmptyLinkedList(
            $entry->value->head,
            $entry->value->tail
        ));
    }

    /**
     * @inheritDoc
     * @return non-empty-list<TV>
     */
    public function toNonEmptyList(): array
    {
        $buffer = [$this->head()];

        foreach ($this->tail() as $elem) {
            $buffer[] = $elem;
        }

        return $buffer;
    }

    /**
     * @inheritDoc
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return new Cons($this->head, $this->tail);
    }

    /**
     * @inheritDoc
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this);
    }

    /**
     * @inheritDoc
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @return NonEmptyArrayList<TV>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe($this);
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
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe($this);
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
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return NonEmptyHashMap<TKI, TVI>
     */
    public function toNonEmptyHashMap(callable $callback): NonEmptyHashMap
    {
        return NonEmptyHashMap::collectPairsUnsafe((function () use ($callback) {
            foreach ($this as $elem) {
                yield $callback($elem);
            }
        })());
    }
}
