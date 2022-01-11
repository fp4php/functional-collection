<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Mutable\HashTable;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\Map;
use Whsv26\Functional\Collection\Immutable\Seq\Nil;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class GroupByOperation extends AbstractOperation
{
    /**
     * @template TKeyOut
     * @psalm-param callable(TValue, TKey): TKeyOut $f
     * @psalm-return HashMap<TKeyOut, LinkedList<TValue>>
     */
    public function __invoke(callable $f): Map
    {
        /**
         * @psalm-var HashTable<TKeyOut, LinkedList<TValue>> $hashTable
         */
        $hashTable = new HashTable();

        foreach ($this->gen as $key => $value) {
            $groupKey = $f($value, $key);

            HashTable::update(
                $hashTable,
                $groupKey,
                HashTable::get($hashTable, $groupKey)
                    ->getOrElse(Nil::getInstance())
                    ->prepended($value)
            );
        }

        return new HashMap($hashTable);
    }
}
