<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Static\Plugin;

use Whsv26\Functional\Collection\ArrayList;
use Whsv26\Functional\Collection\HashSet;
use Whsv26\Functional\Collection\LinkedList;
use Whsv26\Functional\Collection\Seq;
use Whsv26\Functional\Collection\Set;
use Whsv26\Functional\Stream\Stream;

final class CollectionFilterPluginStaticTest
{
    /**
     * @param ArrayList<1|null|2> $in
     * @return ArrayList<1|2>
     */
    public function testArrayListFilter(ArrayList $in): ArrayList
    {
        return $in->filter(fn($e) => null !== $e);
    }

    /**
     * @param LinkedList<1|null|2> $in
     * @return LinkedList<1|2>
     */
    public function testLinkedListFilter(LinkedList $in): LinkedList
    {
        return $in->filter(fn($e) => null !== $e);
    }

    /**
     * @param HashSet<1|null|2> $in
     * @return HashSet<1|2>
     */
    public function testHashSetFilter(HashSet $in): HashSet
    {
        return $in->filter(fn($e) => null !== $e);
    }

    /**
     * @param Stream<1|null|2> $in
     * @return Stream<1|2>
     */
    public function testStreamFilter(Stream $in): Stream
    {
        return $in->filter(fn($e) => null !== $e);
    }

    /**
     * @param Seq<1|null|2> $in
     * @return Seq<1|2>
     */
    public function testSeqFilter(Seq $in): Seq
    {
        return $in->filter(fn($e) => null !== $e);
    }

    /**
     * @param Set<1|null|2> $in
     * @return Set<1|2>
     */
    public function testSetFilter(Set $in): Set
    {
        return $in->filter(fn($e) => null !== $e);
    }

    /**
     * @param array{
     *     ArrayList<1|null|2>,
     *     LinkedList<1|null|2>,
     *     HashSet<1|null|2>,
     *     Seq<1|null|2>,
     *     Set<1|null|2>,
     *     Stream<1|null|2>,
     * } $in
     * @return array{
     *     ArrayList<1|2>,
     *     LinkedList<1|2>,
     *     HashSet<1|2>,
     *     Seq<1|2>,
     *     Set<1|2>,
     *     Stream<1|2>,
     * }
     */
    public function testFilterNotNull(array $in): array
    {
        return [
            $in[0]->filterNotNull(),
            $in[1]->filterNotNull(),
            $in[2]->filterNotNull(),
            $in[3]->filterNotNull(),
            $in[4]->filterNotNull(),
            $in[5]->filterNotNull(),
        ];
    }
}
