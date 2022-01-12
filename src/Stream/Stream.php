<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream;

use Generator;
use LogicException;
use Whsv26\Functional\Collection\ArrayList;
use Whsv26\Functional\Collection\Seq;
use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Core\Unit;
use Whsv26\Functional\Stream\Operations\AppendedAllOperation;
use Whsv26\Functional\Stream\Operations\AppendedOperation;
use Whsv26\Functional\Stream\Operations\ChunksOperation;
use Whsv26\Functional\Stream\Operations\DropOperation;
use Whsv26\Functional\Stream\Operations\DropWhileOperation;
use Whsv26\Functional\Stream\Operations\FilterMapOperation;
use Whsv26\Functional\Stream\Operations\FilterNotNullOperation;
use Whsv26\Functional\Stream\Operations\FilterOfOperation;
use Whsv26\Functional\Stream\Operations\FilterOperation;
use Whsv26\Functional\Stream\Operations\FlatMapOperation;
use Whsv26\Functional\Stream\Operations\GroupAdjacentByOperationOperation;
use Whsv26\Functional\Stream\Operations\GroupByOperation;
use Whsv26\Functional\Stream\Operations\InterleaveOperation;
use Whsv26\Functional\Stream\Operations\IntersperseOperation;
use Whsv26\Functional\Stream\Operations\MapOperation;
use Whsv26\Functional\Stream\Operations\PrependedAllOperation;
use Whsv26\Functional\Stream\Operations\PrependedOperation;
use Whsv26\Functional\Stream\Operations\RepeatNOperation;
use Whsv26\Functional\Stream\Operations\RepeatOperation;
use Whsv26\Functional\Stream\Operations\SortedOperation;
use Whsv26\Functional\Stream\Operations\TailOperation;
use Whsv26\Functional\Stream\Operations\TakeOperation;
use Whsv26\Functional\Stream\Operations\TakeWhileOperation;
use Whsv26\Functional\Stream\Operations\TapOperation;
use Whsv26\Functional\Stream\Operations\UniqueOperation;
use Whsv26\Functional\Stream\Operations\ZipOperation;

/**
 * Note: stream iteration via foreach is terminal operation
 *
 * @psalm-immutable
 * @template-covariant TValue
 * @implements StreamChainableOps<TValue>
 * @implements StreamEmitter<TValue>
 */
final class Stream implements StreamChainableOps, StreamEmitter
{
    /**
     * @var Generator<int, TValue>
     */
    private Generator $emitter;

    /**
     * @psalm-readonly-allow-private-mutation $forked
     */
    private bool $forked = false;

    /**
     * @param iterable<TValue> $emitter
     */
    private function __construct(iterable $emitter)
    {
        $this->emitter = $emitter instanceof Generator
            ? $emitter
            : (function() use ($emitter): Generator {
                foreach ($emitter as $elem) {
                    yield $elem;
                }
            })();
    }

    /**
     * @return CompiledStream<TValue>
     */
    public function compile(): CompiledStream
    {
        return new CompiledStream($this->emitter);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $elem
     * @return self<TValueIn>
     */
    public static function emit(mixed $elem): self
    {
        return self::emits((function () use ($elem) {
            yield $elem;
        })());
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $source
     * @return self<TValueIn>
     */
    public static function emits(iterable $source): self
    {
        return new self($source);
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $const
     * @return self<TValueIn>
     */
    public static function constant(mixed $const): self
    {
        return self::emits((function () use ($const) {
            while (true) {
                yield $const;
            }
        })());
    }

    /**
     * @inheritDoc
     * @return Stream<Unit>
     */
    public static function infinite(): Stream
    {
        return self::constant(Unit::getInstance());
    }

    /**
     * @inheritDoc
     * @param positive-int $by
     * @return self<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): self
    {
        return self::emits((function () use ($start, $stopExclusive, $by) {
            for ($i = $start; $i < $stopExclusive; $i += $by) {
                yield $i;
            }
        })());
    }

    /**
     * @psalm-template TKO
     * @psalm-template TValueIn
     * @param Generator<TValueIn> $gen
     * @return self<TValueIn>
     */
    private function fork(Generator $gen): self
    {
        if (!$this->forked) {
            $this->forked = true;
        } else {
            throw new LogicException('multiple stream forks detected');
        }

        return self::emits($gen);
    }

    /**
     * @template TValueIn
     * @param callable(TValue): TValueIn $callback
     * @return self<TValueIn>
     */
    public function map(callable $callback): self
    {
        return $this->fork(MapOperation::of($this->emitter)($callback));
    }

    /**
     * @inheritDoc
     * @template TKeyIn
     * @template TValueIn
     * @template TKeyOut
     * @psalm-if-this-is Stream<array{TKeyIn, TValueIn}>
     * @param callable(TKeyIn): TKeyOut $callback
     * @return self<array{TKeyOut, TValueIn}>
     */
    public function mapKeys(callable $callback): self
    {
        $mapper = MapOperation::of($this->emitter);

        return $this->fork($mapper(function ($pair) use ($callback) {
            return [$callback($pair[0]), $pair[1]];
        }));
    }

    /**
     * @inheritDoc
     * @template TKeyIn
     * @template TValueIn
     * @template TValueOut
     * @psalm-if-this-is Stream<array{TKeyIn, TValueIn}>
     * @param callable(TValueIn): TValueOut $callback
     * @return self<array{TKeyIn, TValueOut}>
     */
    public function mapValues(callable $callback): self
    {
        $mapper = MapOperation::of($this->emitter);

        return $this->fork($mapper(function ($pair) use ($callback) {
            return [$pair[0], $callback($pair[1])];
        }));
    }

    /**
     * @inheritDoc
     * @template TKeyIn
     * @template TValueIn
     * @psalm-if-this-is Stream<array{TKeyIn, TValueIn}>
     * @param callable(TKeyIn): bool $callback
     * @return self<array{TKeyIn, TValueIn}>
     */
    public function filterKeys(callable $callback): self
    {
        $filter = FilterOperation::of($this->emitter);

        return $this->fork($filter(function ($pair) use ($callback) {
            return $callback($pair[0]);
        }));
    }

    /**
     * @inheritDoc
     * @template TKeyIn
     * @template TValueIn
     * @psalm-if-this-is Stream<array{TKeyIn, TValueIn}>
     * @param callable(TValueIn): bool $callback
     * @return self<array{TKeyIn, TValueIn}>
     */
    public function filterValues(callable $callback): self
    {
        $filter = FilterOperation::of($this->emitter);

        return $this->fork($filter(function ($pair) use ($callback) {
            return $callback($pair[1]);
        }));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $elem
     * @return self<TValue|TValueIn>
     */
    public function appended(mixed $elem): self
    {
        return $this->fork(AppendedOperation::of($this->emitter)($elem));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $suffix
     * @return self<TValue|TValueIn>
     */
    public function appendedAll(iterable $suffix): self
    {
        return $this->fork(AppendedAllOperation::of($this->emitter)($suffix));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $elem
     * @return self<TValue|TValueIn>
     */
    public function prepended(mixed $elem): self
    {
        return $this->fork(PrependedOperation::of($this->emitter)($elem));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $prefix
     * @return self<TValue|TValueIn>
     */
    public function prependedAll(iterable $prefix): self
    {
        return $this->fork(PrependedAllOperation::of($this->emitter)($prefix));
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     * @return self<TValue>
     */
    public function filter(callable $predicate): self
    {
        return $this->fork(FilterOperation::of($this->emitter)($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @param callable(TValue): Option<TValueIn> $callback
     * @return self<TValueIn>
     */
    public function filterMap(callable $callback): self
    {
        return $this->fork(FilterMapOperation::of($this->emitter)($callback));
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function filterNotNull(): self
    {
        return $this->fork(FilterNotNullOperation::of($this->emitter)());
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @param class-string<TValueIn> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     * @return self<TValueIn>
     */
    public function filterOf(string $fqcn, bool $invariant = false): self
    {
        return $this->fork(FilterOfOperation::of($this->emitter)($fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-template TValueIn
     * @param callable(TValue): iterable<TValueIn> $callback
     * @return self<TValueIn>
     */
    public function flatMap(callable $callback): self
    {
        return $this->fork(FlatMapOperation::of($this->emitter)($callback));
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function tail(): self
    {
        return $this->fork(TailOperation::of($this->emitter)());
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     * @return self<TValue>
     */
    public function takeWhile(callable $predicate): self
    {
        return $this->fork(TakeWhileOperation::of($this->emitter)($predicate));
    }

    /**
     * @inheritDoc
     * @param callable(TValue): bool $predicate
     * @return self<TValue>
     */
    public function dropWhile(callable $predicate): self
    {
        return $this->fork(DropWhileOperation::of($this->emitter)($predicate));
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function take(int $length): self
    {
        return $this->fork(TakeOperation::of($this->emitter)($length));
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function drop(int $length): self
    {
        return $this->fork(DropOperation::of($this->emitter)($length));
    }

    /**
     * @inheritDoc
     * @param callable(TValue): void $callback
     * @return self<TValue>
     */
    public function tap(callable $callback): self
    {
        return $this->fork(TapOperation::of($this->emitter)($callback));
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function repeat(): self
    {
        return $this->fork(RepeatOperation::of($this->emitter)());
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function repeatN(int $times): self
    {
        return $this->fork(RepeatNOperation::of($this->emitter)($times));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param TValueIn $separator
     * @return self<TValue|TValueIn>
     */
    public function intersperse(mixed $separator): self
    {
        return $this->fork(IntersperseOperation::of($this->emitter)($separator));
    }

    /**
     * @inheritDoc
     * @return self<TValue>
     */
    public function lines(): self
    {
        return $this->fork(TapOperation::of($this->emitter)(function ($elem) {
            print_r($elem) . PHP_EOL;
        }));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $that
     * @return self<TValue|TValueIn>
     */
    public function interleave(iterable $that): self
    {
        return $this->fork(InterleaveOperation::of($this->emitter)($that));
    }

    /**
     * @inheritDoc
     * @template TValueIn
     * @param iterable<TValueIn> $that
     * @return self<array{TValue, TValueIn}>
     */
    public function zip(iterable $that): self
    {
        return $this->fork(ZipOperation::of($this->emitter)($that));
    }

    /**
     * @inheritDoc
     * @param positive-int $size
     * @return self<Seq<TValue>>
     */
    public function chunks(int $size): self
    {
        $chunks = ChunksOperation::of($this->emitter)($size);

        return $this->fork(MapOperation::of($chunks)(function (array $chunk) {
            return new ArrayList($chunk);
        }));
    }

    /**
     * @inheritDoc
     * @template TDiscriminator
     * @param callable(TValue): TDiscriminator $discriminator
     * @return Stream<array{TDiscriminator, Seq<TValue>}>
     */
    public function groupAdjacentBy(callable $discriminator): Stream
    {
        $adjacent = GroupAdjacentByOperationOperation::of($this->emitter)($discriminator);

        return $this->fork(MapOperation::of($adjacent)(function (array $pair) {
            $pair[1] = new ArrayList($pair[1]);
            return $pair;
        }));
    }

    /**
     * @template TDiscriminator
     * @param callable(TValue): TDiscriminator $discriminator
     * @return self<array{TDiscriminator, Seq<TValue>}>
     */
    public function groupBy(callable $discriminator): self
    {
        return $this->fork(GroupByOperation::of($this->emitter)($discriminator));
    }

    /**
     * @inheritDoc
     * @param callable(TValue, TValue): int $cmp
     * @return self<TValue>
     */
    public function sorted(callable $cmp): self
    {
        return $this->fork(SortedOperation::of($this->emitter)($cmp));
    }

    /**
     * @inheritDoc
     * @param callable(TValue): array-key $callback
     * @return self<TValue>
     */
    public function unique(callable $callback): self
    {
        return $this->fork(UniqueOperation::of($this->emitter)($callback));
    }

    /**
     * @inheritDoc
     * @template TKeyIn
     * @template TValueIn
     * @psalm-if-this-is Stream<array{TKeyIn, TValueIn}>
     * @psalm-return self<TKeyIn>
     * @psalm-suppress MixedArgumentTypeCoercion [Psalm bug]
     */
    public function keys(): self
    {
        return $this->map(fn($pair) => $pair[0]);
    }

    /**
     * @inheritDoc
     * @template TKeyIn
     * @template TValueIn
     * @psalm-if-this-is Stream<array{TKeyIn, TValueIn}>
     * @psalm-return self<TValueIn>
     * @psalm-suppress MixedArgumentTypeCoercion [Psalm bug]
     */
    public function values(): self
    {
        return $this->map(fn($pair) => $pair[1]);
    }
}
