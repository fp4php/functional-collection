<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Immutable\NonEmptyMap;

use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Operations\CountOperation;
use Whsv26\Functional\Stream\Operations\EveryMapOperation;
use Whsv26\Functional\Stream\Operations\EveryOperation;
use Whsv26\Functional\Stream\Operations\KeysOperation;
use Whsv26\Functional\Stream\Operations\MapKeysOperation;
use Whsv26\Functional\Stream\Operations\MapValuesOperation;
use Whsv26\Functional\Stream\Operations\ValuesOperation;
use Generator;
use Whsv26\Functional\Collection\Immutable\Map\Entry;
use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyArrayList;
use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyLinkedList;
use Whsv26\Functional\Collection\Immutable\NonEmptySet\NonEmptyHashSet;
use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\Immutable\Set\HashSet;
use Whsv26\Functional\Collection\Mutable\HashTable;
use Whsv26\Functional\Collection\NonEmptyCollection;
use Whsv26\Functional\Collection\NonEmptyMap;
use Whsv26\Functional\Collection\NonEmptySeq;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @implements NonEmptyMap<TK, TV>
 */
final class NonEmptyHashMap implements NonEmptyMap
{
    /**
     * @internal
     * @param HashMap<TK, TV> $hashMap
     */
    public function __construct(
        private HashMap $hashMap
    ) { }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return Option<self<TKI, TVI>>
     */
    public static function collect(iterable $source): Option
    {
        return self::collectPairs((function () use ($source) {
            foreach ($source as $key => $value) {
                yield [$key, $value];
            }
        })());
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    public static function collectUnsafe(iterable $source): self
    {
        return self::collect($source)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param non-empty-array<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    public static function collectNonEmpty(array $source): self
    {
        return self::collectUnsafe($source);
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return Option<self<TKI, TVI>>
     */
    public static function collectPairs(iterable $source): Option
    {
        /**
         * @psalm-var HashTable<TKI, TVI> $hashTable
         */
        $hashTable = new HashTable();

        foreach ($source as [$key, $value]) {
            $hashTable = $hashTable->update($hashTable, $key, $value);
        }

        $isEmpty = empty($hashTable->table);

        return Option::when(!$isEmpty, fn() => new HashMap($hashTable))
            ->map(fn(HashMap $map) => new self($map));
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collectPairsUnsafe(iterable $source): self
    {
        return self::collectPairs($source)->getUnsafe();
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param non-empty-array<array{TKI, TVI}>|NonEmptyCollection<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collectPairsNonEmpty(array|NonEmptyCollection $source): self
    {
        return self::collectPairsUnsafe($source);
    }

    /**
     * @return Generator<int, array{TK, TV}>
     */
    public function getIterator(): Generator
    {
        return $this->hashMap->getIterator();
    }

    /**
     * @return Generator<TK, TV>
     */
    public function getKeyValueIterator(): Generator
    {
        return $this->hashMap->getKeyValueIterator();
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
     * @return non-empty-list<array{TK, TV}>
     */
    public function toNonEmptyList(): array
    {
        return $this->toNonEmptyArrayList()->toNonEmptyList();
    }

    /**
     * @inheritDoc
     * @return LinkedList<array{TK, TV}>
     */
    public function toLinkedList(): LinkedList
    {
        return LinkedList::collect($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return NonEmptyLinkedList<array{TK, TV}>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList
    {
        return NonEmptyLinkedList::collectUnsafe($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList
    {
        return ArrayList::collect($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return NonEmptyArrayList<array{TK, TV}>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList
    {
        return NonEmptyArrayList::collectUnsafe($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet
    {
        return HashSet::collect($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return NonEmptyHashSet<array{TK, TV}>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe($this->getIterator());
    }

    /**
     * @inheritDoc
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap
    {
        return $this->hashMap;
    }

    /**
     * @inheritDoc
     * @return NonEmptyHashMap<TK, TV>
     */
    public function toNonEmptyHashMap(): NonEmptyHashMap
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return EveryOperation::of($this->getKeyValueIterator())(
            fn($value, $key) => $predicate(new Entry($key, $value))
        );
    }

    /**
     * @inheritDoc
     * @psalm-template TVO
     * @psalm-param callable(Entry<TK, TV>): Option<TVO> $callback
     * @psalm-return Option<self<TK, TVO>>
     */
    public function everyMap(callable $callback): Option
    {
        $hs = EveryMapOperation::of($this->getKeyValueIterator())(
            fn($value, $key) => $callback(new Entry($key, $value))
        );

        return $hs->map(fn($gen) => NonEmptyHashMap::collectUnsafe($gen));
    }

    /**
     * @inheritDoc
     * @param TK $key
     * @return Option<TV>
     */
    public function __invoke(mixed $key): Option
    {
        return $this->get($key);
    }

    /**
     * @inheritDoc
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option
    {
        return $this->hashMap->get($key);
    }

    /**
     * @inheritDoc
     * @template TKI
     * @template TVI
     * @param TKI $key
     * @param TVI $value
     * @return self<TK|TKI, TV|TVI>
     */
    public function updated(mixed $key, mixed $value): self
    {
        return self::collectPairsUnsafe([...$this->toNonEmptyList(), [$key, $value]]);
    }

    /**
     * @inheritDoc
     * @param TK $key
     * @return HashMap<TK, TV>
     */
    public function removed(mixed $key): HashMap
    {
        return $this->hashMap->removed($key);
    }

    /**
     * @inheritDoc
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     * @psalm-return HashMap<TK, TV>
     */
    public function filter(callable $predicate): HashMap
    {
        return $this->hashMap->filter($predicate);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @param callable(Entry<TK, TV>): Option<TVO> $callback
     * @return HashMap<TK, TVO>
     */
    public function filterMap(callable $callback): HashMap
    {
        return $this->hashMap->filterMap($callback);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(Entry<TK, TV>): TVO $callback
     * @psalm-return self<TK, TVO>
     */
    public function map(callable $callback): self
    {
        return $this->mapValues($callback);
    }

    /**
     * @inheritDoc
     * @template TVO
     * @psalm-param callable(Entry<TK, TV>): TVO $callback
     * @psalm-return self<TK, TVO>
     */
    public function mapValues(callable $callback): self
    {
        return self::collectUnsafe(
            MapValuesOperation::of($this->getKeyValueIterator())(
                fn($value, $key) => $callback(new Entry($key, $value))
            )
        );
    }

    /**
     * @inheritDoc
     * @template TKO
     * @psalm-param callable(Entry<TK, TV>): TKO $callback
     * @psalm-return self<TKO, TV>
     */
    public function mapKeys(callable $callback): self
    {
        return self::collectUnsafe(
            MapKeysOperation::of($this->getKeyValueIterator())(
                fn($value, $key) => $callback(new Entry($key, $value))
            )
        );
    }

    /**
     * @inheritDoc
     * @psalm-return NonEmptySeq<TK>
     */
    public function keys(): NonEmptySeq
    {
        return NonEmptyArrayList::collectUnsafe(KeysOperation::of($this->getKeyValueIterator())());
    }

    /**
     * @inheritDoc
     * @psalm-return NonEmptySeq<TV>
     */
    public function values(): NonEmptySeq
    {
        return NonEmptyArrayList::collectUnsafe(ValuesOperation::of($this->getKeyValueIterator())());
    }
}
