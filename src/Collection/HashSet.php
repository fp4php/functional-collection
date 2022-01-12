<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Iterator;
use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Stream;

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
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $val
     * @return self<TValueIn>
     */
    public static function singleton(mixed $val): self
    {
        return self::collect([$val]);
    }

    /**
     * @inheritDoc
     * @return self<empty>
     */
    public static function empty(): self
    {
        return self::collect([]);
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
     * @return Stream<TValue>
     */
    public function stream(): Stream
    {
        return Stream::emits($this->getIterator());
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->knownSize = $this->knownSize ?? $this->stream()
            ->compile()
            ->count();
    }

    /**
     * @inheritDoc
     * @return list<TValue>
     */
    public function toList(): array
    {
        return $this->stream()->compile()->toList();
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
        return $this->stream()->compile()->every($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param class-string<TValueIn> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->stream()->compile()->everyOf($fqcn, $invariant);
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
            ->map(fn(Stream $stream) => $stream->compile()->toHashSet());
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return $this->stream()
            ->compile()
            ->exists($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param class-string<TValueIn> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->stream()
            ->compile()
            ->existsOf($fqcn, $invariant);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return Option<TValue>
     */
    public function first(callable $predicate): Option
    {
        return $this->stream()
            ->compile()
            ->first($predicate);
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
        return $this->stream()
            ->compile()
            ->firstOf($fqcn, $invariant);
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
        return $this->stream()
            ->compile()
            ->fold($init, $callback);
    }

    /**
     * @inheritDoc
     * @template TA
     * @psalm-param callable(TValue|TA, TValue): (TValue|TA) $callback
     * @psalm-return Option<TValue|TA>
     */
    public function reduce(callable $callback): Option
    {
        return $this->stream()
            ->compile()
            ->reduce($callback);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TValue>
     */
    public function head(): Option
    {
        return $this->stream()
            ->compile()
            ->head();
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return Option<TValue>
     */
    public function last(callable $predicate): Option
    {
        return $this->stream()
            ->compile()
            ->last($predicate);
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TValue>
     */
    public function firstElement(): Option
    {
        return $this->stream()
            ->compile()
            ->firstElement();
    }

    /**
     * @inheritDoc
     * @psalm-return Option<TValue>
     */
    public function lastElement(): Option
    {
        return $this->stream()
            ->compile()
            ->lastElement();
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
        return $this->stream()
            ->tail()
            ->compile()
            ->toHashSet();
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return self<TValue>
     */
    public function filter(callable $predicate): self
    {
        return $this->stream()
            ->filter($predicate)
            ->compile()
            ->toHashSet();
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
        return $this->stream()
            ->filterOf($fqcn, $invariant)
            ->compile()
            ->toHashSet();
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
     */
    public function filterNotNull(): self
    {
        return $this->stream()
            ->filterNotNull()
            ->compile()
            ->toHashSet();
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param callable(TValue): Option<TValueIn> $callback
     * @psalm-return self<TValueIn>
     */
    public function filterMap(callable $callback): self
    {
        return $this->stream()
            ->filterMap($callback)
            ->compile()
            ->toHashSet();
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @psalm-param callable(TValue): iterable<TValueIn> $callback
     * @psalm-return self<TValueIn>
     */
    public function flatMap(callable $callback): self
    {
        return $this->stream()
            ->flatMap($callback)
            ->compile()
            ->toHashSet();
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @psalm-param callable(TValue): TValueIn $callback
     * @psalm-return self<TValueIn>
     */
    public function map(callable $callback): self
    {
        return $this->stream()
            ->map($callback)
            ->compile()
            ->toHashSet();
    }

    /**
     * @inheritDoc
     * @param callable(TValue): void $callback
     * @psalm-return self<TValue>
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
     */
    public function isEmpty(): bool
    {
        return $this->map->isEmpty();
    }

    /**
     * @inheritDoc
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
