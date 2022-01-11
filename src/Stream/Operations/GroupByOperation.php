<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Collection\Map;
use Whsv26\Functional\Collection\Map\HashMap;
use Whsv26\Functional\Collection\Map\HashTable;
use Whsv26\Functional\Collection\Seq\LinkedList;
use Whsv26\Functional\Collection\Seq\Nil;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TValue>
 */
class GroupByOperation extends AbstractOperation
{
    /**
     * @template TKeyOut
     * @psalm-param callable(TValue): TKeyOut $f
     * @psalm-return HashMap<TKeyOut, LinkedList<TValue>>
     */
    public function __invoke(callable $f): Map
    {
        /**
         * @psalm-var HashTable<TKeyOut, LinkedList<TValue>> $hashTable
         */
        $hashTable = new HashTable();

        foreach ($this->gen as $value) {
            $groupKey = $f($value);

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
