<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Immutable\Set;

use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Operations\CountOperation;
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
use Whsv26\Functional\Stream\Operations\HeadOperation;
use Whsv26\Functional\Stream\Operations\LastOperation;
use Whsv26\Functional\Stream\Operations\MapValuesOperation;
use Whsv26\Functional\Stream\Operations\ReduceOperation;
use Whsv26\Functional\Stream\Operations\TailOperation;
use Whsv26\Functional\Stream\Operations\TapOperation;
use Whsv26\Functional\Stream\Stream;
use Iterator;
use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\NonEmptySet;
use Whsv26\Functional\Collection\Set;

/**
 * @template-covariant TV
 * @psalm-immutable
 * @implements Set<TV>
 */
final class HashSet implements Set
{
    /**
     * @param HashMap<TV, TV> $map
     */
    private function __construct(private HashMap $map) { }

    /**
     * @inheritDoc
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self
    {
        return new self(ArrayList::collect($source)->toHashMap(fn(mixed $elem) => [$elem, $elem]));
    }

    /**
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return (function () {
            foreach ($this->map as $pair) {
                yield $pair[1];
            }
        })();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return CountOperation::of($this->getIterator())();
    }

    /**
     * @inheritDoc
     * @return list<TV>
     */
    public function toArray(): array
    {
        return Stream::emits($this->getIterator())->compile()->toList();
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
        return ArrayList::collect($this);
    }

    /**
     * @inheritDoc
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet
    {
        return $this;
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
     * @psalm-param TV $element
     */
    public function __invoke(mixed $element): bool
    {
        return $this->contains($element);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return EveryOperation::of($this)($predicate);
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
            ->map(fn($gen) => HashSet::collect($gen));
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
     * @psalm-return Option<TV>
     */
    public function head(): Option
    {
        return HeadOperation::of($this->getIterator())();
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
        return FirstOperation::of($this->getIterator())();
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TV>
     */
    public function lastElement(): Option
    {
        return LastOperation::of($this->getIterator())();
    }

    /**
     * @inheritDoc
     * @psalm-param TV $element
     */
    public function contains(mixed $element): bool
    {
        return $this->map->get($element)->isNonEmpty();
    }

    /**
     * @inheritDoc
     * @template TVI
     * @param TVI $element
     * @return self<TV|TVI>
     */
    public function updated(mixed $element): self
    {
        return new self($this->map->updated($element, $element));
    }

    /**
     * @inheritDoc
     * @param TV $element
     * @return self<TV>
     */
    public function removed(mixed $element): self
    {
        return new self($this->map->removed($element));
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
     * @psalm-return self<TV>
     */
    public function filterNotNull(): self
    {
        return self::collect(FilterNotNullOperation::of($this->getIterator())());
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
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::collect(MapValuesOperation::of($this)($callback));
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

    public function isEmpty():bool
    {
        return $this->map->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function subsetOf(Set|NonEmptySet $superset): bool
    {
        $isSubset = true;

        foreach ($this as $elem) {
            if (!$superset($elem)) {
                $isSubset = false;
                break;
            }
        }

        return $isSubset;
    }

    /**
     * @inheritDoc
     * @param Set<TV>|NonEmptySet<TV> $that
     * @return Set<TV>
     */
    public function intersect(Set|NonEmptySet $that): Set
    {
        return $this->filter(fn($elem) => /** @var TV $elem */ $that($elem));
    }

    /**
     * @inheritDoc
     * @param Set<TV>|NonEmptySet<TV> $that
     * @return Set<TV>
     */
    public function diff(Set|NonEmptySet $that): Set
    {
        return $this->filter(fn($elem) => /** @var TV $elem */ !$that($elem));
    }
}
