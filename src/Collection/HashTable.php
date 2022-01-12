<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;

/**
 * @internal
 * @template TKey
 * @template TValue
 * @psalm-type hash = string
 * @psalm-suppress ImpureMethodCall, ImpurePropertyFetch
 */
final class HashTable
{
    /**
     * @var array<hash, list<array{TKey, TValue}>>
     */
    public array $table = [];

    /**
     * @psalm-pure
     * @template TKeyIn
     * @template TValueIn
     * @param HashTable<TKeyIn, TValueIn> $hashTable
     * @param TKeyIn $key
     * @return Option<TValueIn>
     */
    public static function get(HashTable $hashTable, mixed $key): Option
    {
        $hash = (string) HashComparator::computeHash($key);
        $elem = null;

        foreach ($hashTable->table[$hash] ?? [] as [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $elem = $v;
            }
        }

        return Option::fromNullable($elem);
    }

    /**
     * @psalm-pure
     * @template TKeyIn
     * @template TValueIn
     * @param TKeyIn $key
     * @param TValueIn $value
     * @param HashTable<TKeyIn, TValueIn> $hashTable
     * @return HashTable<TKeyIn, TValueIn>
     * @psalm-suppress PropertyTypeCoercion
     */
    public static function update(HashTable $hashTable, mixed $key, mixed $value): HashTable
    {
        $hash = (string) HashComparator::computeHash($key);

        if (!isset($hashTable->table[$hash])) {
            $hashTable->table[$hash] = [];
        }

        $replacedPos = -1;

        foreach ($hashTable->table[$hash] as $idx => [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $replacedPos = $idx;
                $hashTable->table[$hash][$idx][1] = $value;
            }
        }

        if ($replacedPos < 0) {
            $hashTable->table[$hash][] = [$key, $value];
        }

        return $hashTable;
    }
}
