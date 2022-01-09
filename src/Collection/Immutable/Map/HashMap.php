<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Immutable\Map;

use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Operations\CountOperation;
use Generator;
use Whsv26\Functional\Collection\Map;
use Whsv26\Functional\Collection\Mutable\HashTable;
use Whsv26\Functional\Collection\Seq;
use Whsv26\Functional\Stream\Stream;

/**
 * @template TKey
 * @template-covariant TValue
 * @psalm-immutable
 * @implements Map<TKey, TValue>
 */
final class HashMap implements Map
{
    private bool $empty;

    /**
     * @internal
     * @psalm-param HashTable<TKey, TValue> $hashTable
     */
    public function __construct(private HashTable $hashTable)
    {
        $this->empty = empty($hashTable->table);
    }

    /**
     * @inheritDoc
     * @template TKeyIn
     * @template TValueIn
     * @param iterable<TKeyIn, TValueIn> $source
     * @return self<TKeyIn, TValueIn>
     */
    public static function collect(iterable $source): self
    {
        return self::collectPairs((function () use ($source) {
            foreach ($source as $key => $value) {
                yield [$key, $value];
            }
        })());
    }

    /**
     * @inheritDoc
     * @template TKeyIn
     * @template TValueIn
     * @param iterable<array{TKeyIn, TValueIn}> $source
     * @return self<TKeyIn, TValueIn>
     */
    public static function collectPairs(iterable $source): self
    {
        /**
         * @psalm-var HashTable<TKeyIn, TValueIn> $hashTable
         */
        $hashTable = new HashTable();

        foreach ($source as [$key, $value]) {
            $hashTable = $hashTable->update($hashTable, $key, $value);
        }

        return new HashMap($hashTable);
    }

    /**
     * @return Generator<int, array{TKey, TValue}>
     */
    public function getIterator(): Generator
    {
        foreach ($this->hashTable->table as $bucket) {
            foreach ($bucket as $pair) {
                yield $pair;
            }
        }
    }

    public function getKeyValueIterator(): Generator
    {
        foreach ($this as [$key, $value]) {
            yield $key => $value;
        }
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
     * @return Stream<array{TKey, TValue}>
     */
    public function toStream(): Stream
    {
        return Stream::emits($this->getIterator());
    }

    /**
     * @inheritDoc
     * @template TKeyIn
     * @template TValueIn
     * @param TKeyIn $key
     * @param TValueIn $value
     * @return self<TKey|TKeyIn, TValue|TValueIn>
     */
    public function updated(mixed $key, mixed $value): self
    {
        return $this->toStream()
            ->appended([$key, $value])
            ->compile()
            ->toHashMap();
    }

    /**
     * @inheritDoc
     * @param TKey $key
     * @return self<TKey, TValue>
     */
    public function removed(mixed $key): self
    {
        return $this->filterKeys(fn($k) => $k !== $key);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return self<TKey, TValue>
     */
    public function filterValues(callable $predicate): self
    {
        return $this->toStream()
            ->filterValues($predicate)
            ->compile()
            ->toHashMap();
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TKey): bool $predicate
     * @psalm-return self<TKey, TValue>
     */
    public function filterKeys(callable $predicate): self
    {
        return $this->toStream()
            ->filterKeys($predicate)
            ->compile()
            ->toHashMap();
    }

    /**
     * @inheritDoc
     * @template TValueOut
     * @param callable(TValue): Option<TValueOut> $callback
     * @return self<TKey, TValueOut>
     */
    public function filterMapValues(callable $callback): self
    {
        return $this->toStream()
            ->mapValues($callback)
            ->filterValues(fn(Option $key) => $key->isSome())
            ->mapValues(fn(Option $some) => $some->getUnsafe()) // TODO refinement
            ->compile()
            ->toHashMap();
    }

    /**
     * @inheritDoc
     * @psalm-template TKeyOut
     * @psalm-param callable(TKey): Option<TKeyOut> $callback
     * @psalm-return self<TKeyOut, TValue>
     */
    public function filterMapKeys(callable $callback): self
    {
        return $this->toStream()
            ->mapKeys($callback)
            ->filterKeys(fn(Option $key) => $key->isSome())
            ->mapKeys(fn(Option $some) => $some->getUnsafe()) // TODO refinement
            ->compile()
            ->toHashMap();
    }

    /**
     * @inheritDoc
     * @template TValueOut
     * @psalm-param callable(TValue): TValueOut $callback
     * @psalm-return self<TKey, TValueOut>
     */
    public function mapValues(callable $callback): self
    {
        return $this->toStream()
            ->mapValues($callback)
            ->compile()
            ->toHashMap();
    }

    /**
     * @inheritDoc
     * @template TKeyOut
     * @psalm-param callable(TKey): TKeyOut $callback
     * @psalm-return self<TKeyOut, TValue>
     */
    public function mapKeys(callable $callback): self
    {
        return $this->toStream()
            ->mapKeys($callback)
            ->compile()
            ->toHashMap();
    }

    /**
     * @inheritDoc
     * @psalm-return Seq<TKey>
     */
    public function keys(): Seq
    {
        return $this->toStream()
            ->map(fn($pair) => $pair[0])
            ->compile()
            ->toArrayList();
    }

    /**
     * @inheritDoc
     * @psalm-return Seq<TValue>
     */
    public function values(): Seq
    {
        return $this->toStream()
            ->map(fn($pair) => $pair[1])
            ->compile()
            ->toArrayList();
    }

    public function isEmpty():bool
    {
        return $this->empty;
    }

    /**
     * @inheritDoc
     * @param TKey $key
     * @return Option<TValue>
     */
    public function __invoke(mixed $key): Option
    {
        return $this->get($key);
    }

    /**
     * @inheritDoc
     * @param TKey $key
     * @return Option<TValue>
     * @psalm-suppress ImpureMethodCall
     */
    public function get(mixed $key): Option
    {
        $elem = null;
        $hash = (string) HashComparator::computeHash($key);

        $bucket = Option::fromNullable($this->hashTable->table[$hash] ?? null)
            ->getOrElse([]);

        foreach ($bucket as [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $elem = $v;
            }
        }

        return Option::fromNullable($elem);
    }
}
