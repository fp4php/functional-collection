<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Generator;
use Whsv26\Functional\Core\Option;
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
     * @psalm-allow-private-mutation
     */
    private ?int $knownSize;

    /**
     * @internal
     * @param HashTable<TKey, TValue> $hashTable
     */
    public function __construct(private HashTable $hashTable)
    {
        $this->empty = empty($hashTable->table);
        $this->knownSize = null;
    }

    /**
     * @inheritDoc
     * @return bool
     */
    public function isNonEmpty(): bool
    {
        return !$this->empty;
    }

    /**
     * @inheritDoc
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->empty;
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
         * @var HashTable<TKeyIn, TValueIn> $hashTable
         */
        $hashTable = new HashTable();

        foreach ($source as [$key, $value]) {
            $hashTable = $hashTable->update($hashTable, $key, $value);
        }

        return new HashMap($hashTable);
    }

    /**
     * @inheritDoc
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
     * @return Stream<array{TKey, TValue}>
     */
    public function stream(): Stream
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
        return $this->stream()
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
        return $this->stream()
            ->filterKeys(fn($k) => $k !== $key)
            ->compile()
            ->toHashMap();
    }

    /**
     * @inheritDoc
     * @return Seq<TKey>
     */
    public function keys(): Seq
    {
        return $this->stream()
            ->map(fn($pair) => $pair[0])
            ->compile()
            ->toArrayList();
    }

    /**
     * @inheritDoc
     * @return Seq<TValue>
     */
    public function values(): Seq
    {
        return $this->stream()
            ->map(fn($pair) => $pair[1])
            ->compile()
            ->toArrayList();
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

        $bucket = $this->hashTable->table[$hash] ?? [];

        foreach ($bucket as [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $elem = $v;
            }
        }

        return Option::fromNullable($elem);
    }

    /**
     * @inheritDoc
     * @template TKeyIn of array-key
     * @template TValueIn
     * @psalm-if-this-is HashMap<TKeyIn, TValueIn>
     * @return array<TKeyIn, TValueIn>
     */
    public function toArray(): array
    {
        return $this->stream()
            ->compile()
            ->toArray();
    }
}
