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
use Whsv26\Functional\Collection\Set;

/**
 * @template-covariant TValue
 * @psalm-immutable
 * @implements Set<TValue>
 */
final class HashSet implements Set
{
    /**
     * @psalm-allow-private-mutation
     */
    private ?int $knownSize;

    /**
     * @param HashMap<TValue, TValue> $map
     */
    private function __construct(
        private HashMap $map
    ) { }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $source
     * @return self<TValueIn>
     */
    public static function collect(iterable $source): self
    {
        $hashMap = Stream::emits($source)
            ->map(fn($elem) => [$elem, $elem])
            ->compile()
            ->toHashMap();

        return new self($hashMap);
    }

    /**
     * @return Iterator<int, TValue>
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
        return $this->knownSize = $this->knownSize
            ?? CountOperation::of($this->getIterator())();
    }

    /**
     * @inheritDoc
     * @return list<TValue>
     */
    public function toList(): array
    {
        return Stream::emits($this->getIterator())->compile()->toList();
    }

    /**
     * @inheritDoc
     * @psalm-param TValue $element
     */
    public function __invoke(mixed $element): bool
    {
        return $this->contains($element);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return EveryOperation::of($this)($predicate);
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
            ->map(fn($gen) => HashSet::collect($gen));
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
        return HeadOperation::of($this->getIterator())();
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
        return FirstOperation::of($this->getIterator())();
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
     * @psalm-param TValue $element
     */
    public function contains(mixed $element): bool
    {
        return $this->map->get($element)->isNonEmpty();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $element
     * @return self<TValue|TValueIn>
     */
    public function updated(mixed $element): self
    {
        return new self($this->map->updated($element, $element));
    }

    /**
     * @inheritDoc
     * @param TValue $element
     * @return self<TValue>
     */
    public function removed(mixed $element): self
    {
        return new self($this->map->removed($element));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
     */
    public function tail(): self
    {
        return self::collect(TailOperation::of($this->getIterator())());
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
     * @psalm-return self<TValue>
     */
    public function filterNotNull(): self
    {
        return self::collect(FilterNotNullOperation::of($this->getIterator())());
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
     * @template TValueIn
     * @psalm-param callable(TValue): TValueIn $callback
     * @psalm-return self<TValueIn>
     */
    public function map(callable $callback): self
    {
        return self::collect(MapValuesOperation::of($this)($callback));
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
     * @psalm-pure
     */
    public function isEmpty(): bool
    {
        return $this->map->isEmpty();
    }

    /**
     * @inheritDoc
     * @psalm-pure
     */
    public function isNonEmpty(): bool
    {
        return $this->map->isNonEmpty();
    }

    /**
     * @inheritDoc
     */
    public function subsetOf(Set $superset): bool
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
     * @param Set<TValue> $that
     * @return Set<TValue>
     */
    public function intersect(Set $that): Set
    {
        return $this->filter(fn($elem) => /** @var TValue $elem */ $that($elem));
    }

    /**
     * @inheritDoc
     * @param Set<TValue> $that
     * @return Set<TValue>
     */
    public function diff(Set $that): Set
    {
        return $this->filter(fn($elem) => /** @var TValue $elem */ !$that($elem));
    }
}
