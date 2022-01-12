<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream;

use Generator;
use LogicException;
use SplFileObject;
use Whsv26\Functional\Collection\ArrayList;
use Whsv26\Functional\Collection\HashMap;
use Whsv26\Functional\Collection\HashSet;
use Whsv26\Functional\Collection\LinkedList;
use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Operations\AtOperation;
use Whsv26\Functional\Stream\Operations\CountOperation;
use Whsv26\Functional\Stream\Operations\EveryMapOperation;
use Whsv26\Functional\Stream\Operations\EveryOfOperation;
use Whsv26\Functional\Stream\Operations\EveryOperation;
use Whsv26\Functional\Stream\Operations\ExistsOfOperation;
use Whsv26\Functional\Stream\Operations\ExistsOperation;
use Whsv26\Functional\Stream\Operations\FirstOfOperation;
use Whsv26\Functional\Stream\Operations\FirstOperation;
use Whsv26\Functional\Stream\Operations\FoldOperation;
use Whsv26\Functional\Stream\Operations\HeadOperation;
use Whsv26\Functional\Stream\Operations\LastOfOperation;
use Whsv26\Functional\Stream\Operations\LastOperation;
use Whsv26\Functional\Stream\Operations\MkStringOperation;
use Whsv26\Functional\Stream\Operations\ReduceOperation;

/**
 * Note: stream iteration via foreach is terminal operation
 *
 * @psalm-immutable
 * @template-covariant TValue
 * @implements CompiledStreamTerminalOps<TValue>
 * @implements CompiledStreamCastableOps<TValue>
 */
final class CompiledStream implements CompiledStreamTerminalOps, CompiledStreamCastableOps
{
    /**
     * @var Generator<int, TValue>
     */
    private Generator $emitter;

    /**
     * @psalm-readonly-allow-private-mutation $drained
     */
    private bool $drained = false;

    /**
     * @internal
     * @param iterable<TValue> $emitter
     */
    public function __construct(iterable $emitter)
    {
        $gen = function() use ($emitter): Generator {
            foreach ($emitter as $elem) {
                yield $elem;
            }
        };

        $this->emitter = $gen();
    }

    /**
     * @template T
     * @param T $iter
     * @return T
     */
    private function leaf(mixed $iter): mixed
    {
        $this->drained = !$this->drained
            ? true
            : throw new LogicException('Can not drain already drained stream');

        return $iter;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->leaf(CountOperation::of($this->emitter)());
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     */
    public function every(callable $predicate): bool
    {
        return $this->leaf(EveryOperation::of($this->emitter)($predicate));
    }

    /**
     * @inheritDoc
     * @template TValueOut
     * @param class-string<TValueOut> $fqcn
     * @param bool $invariant
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->leaf(EveryOfOperation::of($this->emitter)($fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     */
    public function exists(callable $predicate): bool
    {
        return $this->leaf(ExistsOperation::of($this->emitter)($predicate));
    }

    /**
     * @inheritDoc
     * @template TValueOut
     * @param class-string<TValueOut> $fqcn
     * @param bool $invariant
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool
    {
        return $this->leaf(ExistsOfOperation::of($this->emitter)($fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     * @return Option<TValue>
     */
    public function first(callable $predicate): Option
    {
        return $this->leaf(FirstOperation::of($this->emitter)($predicate));
    }

    /**
     * @inheritDoc
     * @template TValueOut
     * @param class-string<TValueOut> $fqcn
     * @param bool $invariant
     * @return Option<TValueOut>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option
    {
        return $this->leaf(FirstOfOperation::of($this->emitter)($fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @template TValueOut
     * @param class-string<TValueOut> $fqcn
     * @param bool $invariant
     * @return Option<TValueOut>
     */
    public function lastOf(string $fqcn, bool $invariant = false): Option
    {
        return $this->leaf(LastOfOperation::of($this->emitter)($fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @template TA
     * @param TA $init initial accumulator value
     * @param callable(TA, TValue): TA $callback (accumulator, current element): new accumulator
     * @return TA
     */
    public function fold(mixed $init, callable $callback): mixed
    {
        return $this->leaf(FoldOperation::of($this->emitter)($init, $callback));
    }

    /**
     * @inheritDoc
     * @template TA
     * @param callable(TValue|TA, TValue): (TValue|TA) $callback
     * @return Option<TValue|TA>
     */
    public function reduce(callable $callback): Option
    {
        return $this->leaf(ReduceOperation::of($this->emitter)($callback));
    }

    /**
     * @inheritDoc
     * @return Option<TValue>
     */
    public function head(): Option
    {
        return $this->leaf(HeadOperation::of($this->emitter)());
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     * @return Option<TValue>
     */
    public function last(callable $predicate): Option
    {
        return $this->leaf(LastOperation::of($this->emitter)($predicate));
    }

    /**
     * @inheritDoc
     * @return Option<TValue>
     */
    public function firstElement(): Option
    {
        return $this->leaf(FirstOperation::of($this->emitter)());
    }

    /**
     * @inheritDoc
     * @return Option<TValue>
     */
    public function lastElement(): Option
    {
        return $this->leaf(LastOperation::of($this->emitter)());
    }

    /**
     * @inheritDoc
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string
    {
        return $this->leaf(MkStringOperation::of($this->emitter)($start, $sep, $end));
    }

    /**
     * @inheritDoc
     */
    public function drain(): void
    {
        foreach ($this->leaf($this->emitter) as $ignored) { }
    }

    /**
     * @inheritDoc
     * @return list<TValue>
     */
    public function toList(): array
    {
        $list = [];

        foreach ($this->emitter as $val) {
            $list[] = $val;
        }

        return $this->leaf($list);
    }

    /**
     * @inheritDoc
     * @return Option<non-empty-list<TValue>>
     */
    public function toNonEmptyList(): Option
    {
        $list = [];

        foreach ($this->emitter as $val) {
            $list[] = $val;
        }

        return $this->leaf(empty($list) ? Option::none() : Option::some($list));
    }

    /**
     * @inheritDoc
     * @template TKeyIn of array-key
     * @template TValueIn
     * @psalm-if-this-is CompiledStream<array{TKeyIn, TValueIn}>
     * @return array<TKeyIn, TValueIn>
     */
    public function toArray(): array
    {
        $arr = [];

        foreach ($this->emitter as [$key, $val]) {
            $arr[$key] = $val;
        }

        return $this->leaf($arr);
    }

    /**
     * @inheritDoc
     * @template TKeyIn of array-key
     * @template TValueIn
     * @psalm-if-this-is CompiledStream<array{TKeyIn, TValueIn}>
     * @return Option<non-empty-array<TKeyIn, TValueIn>>
     */
    public function toNonEmptyArray(): Option
    {
        $arr = [];

        foreach ($this->emitter as [$key, $val]) {
            $arr[$key] = $val;
        }

        return $this->leaf(empty($arr) ? Option::none() : Option::some($arr));
    }

    /**
     * @inheritDoc
     * @return LinkedList<TValue>
     */
    public function toLinkedList(): LinkedList
    {
        return $this->leaf(LinkedList::collect($this->emitter));
    }

    /**
     * @inheritDoc
     * @return ArrayList<TValue>
     */
    public function toArrayList(): ArrayList
    {
        return $this->leaf(ArrayList::collect($this->emitter));
    }

    /**
     * @inheritDoc
     * @return HashSet<TValue>
     */
    public function toHashSet(): HashSet
    {
        return $this->leaf(HashSet::collect($this->emitter));
    }

    /**
     * @inheritDoc
     * @template TKeyIn
     * @template TValueIn
     * @psalm-if-this-is CompiledStream<array{TKeyIn, TValueIn}>
     * @return HashMap<TKeyIn, TValueIn>
     */
    public function toHashMap(): HashMap
    {
        return $this->leaf(HashMap::collectPairs($this->emitter));
    }

    /**
     * @inheritDoc
     * @return Generator<TValue>
     */
    public function toGenerator(): Generator
    {
        return $this->leaf($this->emitter);
    }

    /**
     * @inheritDoc
     */
    public function toFile(string $path, bool $append = false): void
    {
        $file = new SplFileObject($path, $append ? 'a' : 'w');

        foreach ($this->emitter as $elem) {
            $file->fwrite((string) $elem);
        }

        $file = null;

        $this->leaf($file);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param callable(TValue): Option<TValueIn> $callback
     * @return Option<Stream<TValueIn>>
     */
    public function everyMap(callable $callback): Option
    {
        return $this->leaf(EveryMapOperation::of($this->emitter)($callback));
    }

    /**
     * @inheritDoc
     * @return Option<TValue>
     */
    public function at(int $index): Option
    {
        return $this->leaf(AtOperation::of($this->emitter)($index));
    }
}
