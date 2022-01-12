<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Whsv26\Functional\Collection\HashTable;
use Whsv26\Functional\Collection\LinkedList;
use Whsv26\Functional\Collection\Nil;
use Whsv26\Functional\Collection\Seq;
use Whsv26\Functional\Stream\AbstractStreamOperation;
use Whsv26\Functional\Stream\Stream;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class GroupByOperation extends AbstractStreamOperation
{
    /**
     * @template TKeyOut
     * @param callable(TValue): TKeyOut $f
     * @return Generator<array{TKeyOut, Seq<TValue>}>
     */
    public function __invoke(callable $f): Generator
    {
        /**
         * @var HashTable<TKeyOut, LinkedList<TValue>> $hashTable
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

        return Stream::emits($hashTable->table)
            ->flatMap(fn(array $bucket) => $bucket)
            ->compile()
            ->toGenerator();
    }
}
