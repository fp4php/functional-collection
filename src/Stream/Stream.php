<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream;

use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
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
use Whsv26\Functional\Stream\Operations\InterleaveOperation;
use Whsv26\Functional\Stream\Operations\IntersperseOperation;
use Whsv26\Functional\Stream\Operations\MapValuesOperation;
use Whsv26\Functional\Stream\Operations\PrependedAllOperation;
use Whsv26\Functional\Stream\Operations\PrependedOperation;
use Whsv26\Functional\Stream\Operations\RepeatNOperation;
use Whsv26\Functional\Stream\Operations\RepeatOperation;
use Whsv26\Functional\Stream\Operations\SortedOperation;
use Whsv26\Functional\Stream\Operations\TailOperation;
use Whsv26\Functional\Stream\Operations\TakeOperation;
use Whsv26\Functional\Stream\Operations\TakeWhileOperation;
use Whsv26\Functional\Stream\Operations\TapOperation;
use Whsv26\Functional\Stream\Operations\ZipOperation;
use Generator;
use LogicException;

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
        $gen = function() use ($emitter): Generator {
            foreach ($emitter as $elem) {
                yield $elem;
            }
        };

        $this->emitter = $gen();
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
     * @template TValueI
     * @param TValueI $elem
     * @return self<TValueI>
     */
    public static function emit(mixed $elem): self
    {
        return self::emits((function () use ($elem) {
            yield $elem;
        })());
    }

    /**
     * @inheritDoc
     * @template TValueI
     * @param iterable<TValueI> $source
     * @return self<TValueI>
     */
    public static function emits(iterable $source): self
    {
        return new self($source);
    }

    /**
     * @inheritDoc
     * @param 0|positive-int $seconds
     * @return self<int>
     */
    public static function awakeEvery(int $seconds): self
    {
        return self::emits((function () use ($seconds) {
            $elapsed = 0;
            $prevTime = time();

            while (true) {
                /** @psalm-suppress PossiblyInvalidArgument */
                sleep($seconds);

                $curTime = time();
                $elapsed += $curTime - $prevTime;
                $prevTime = $curTime;

                yield $elapsed;
            }
        })());
    }

    /**
     * @inheritDoc
     * @template TValueI
     * @param TValueI $const
     * @return self<TValueI>
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
     * @psalm-template TValueO
     * @psalm-param Generator<TValueO> $gen
     * @psalm-return self<TValueO>
     */
    private function fork(Generator $gen): self
    {
        $this->forked = !$this->forked
            ? $this->forked = true
            : throw new LogicException('multiple stream forks detected');

        return self::emits($gen);
    }

    /**
     * @template TValueO
     * @psalm-param callable(TValue): TValueO $callback
     * @psalm-return self<TValueO>
     */
    public function map(callable $callback): self
    {
        return $this->fork(MapValuesOperation::of($this->emitter)($callback));
    }

    /**
     * @inheritDoc
     * @template TValueI
     * @psalm-param TValueI $elem
     * @psalm-return self<TValue|TValueI>
     */
    public function appended(mixed $elem): self
    {
        return $this->fork(AppendedOperation::of($this->emitter)($elem));
    }

    /**
     * @inheritDoc
     * @template TValueI
     * @psalm-param iterable<TValueI> $suffix
     * @psalm-return self<TValue|TValueI>
     */
    public function appendedAll(iterable $suffix): self
    {
        return $this->fork(AppendedAllOperation::of($this->emitter)($suffix));
    }

    /**
     * @inheritDoc
     * @template TValueI
     * @psalm-param TValueI $elem
     * @psalm-return self<TValue|TValueI>
     */
    public function prepended(mixed $elem): self
    {
        return $this->fork(PrependedOperation::of($this->emitter)($elem));
    }

    /**
     * @inheritDoc
     * @template TValueI
     * @psalm-param iterable<TValueI> $prefix
     * @psalm-return self<TValue|TValueI>
     */
    public function prependedAll(iterable $prefix): self
    {
        return $this->fork(PrependedAllOperation::of($this->emitter)($prefix));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return self<TValue>
     */
    public function filter(callable $predicate): self
    {
        return $this->fork(FilterOperation::of($this->emitter)($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-template TValueO
     * @psalm-param callable(TValue): Option<TValueO> $callback
     * @psalm-return self<TValueO>
     */
    public function filterMap(callable $callback): self
    {
        return $this->fork(FilterMapOperation::of($this->emitter)($callback));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
     */
    public function filterNotNull(): self
    {
        return $this->fork(FilterNotNullOperation::of($this->emitter)());
    }

    /**
     * @inheritDoc
     * @psalm-template TValueO
     * @psalm-param class-string<TValueO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return self<TValueO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): self
    {
        return $this->fork(FilterOfOperation::of($this->emitter)($fqcn, $invariant));
    }

    /**
     * @inheritDoc
     * @psalm-template TValueO
     * @psalm-param callable(TValue): iterable<TValueO> $callback
     * @psalm-return self<TValueO>
     */
    public function flatMap(callable $callback): self
    {
        return $this->fork(FlatMapOperation::of($this->emitter)($callback));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
     */
    public function tail(): self
    {
        return $this->fork(TailOperation::of($this->emitter)());
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return self<TValue>
     */
    public function takeWhile(callable $predicate): self
    {
        return $this->fork(TakeWhileOperation::of($this->emitter)($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue): bool $predicate
     * @psalm-return self<TValue>
     */
    public function dropWhile(callable $predicate): self
    {
        return $this->fork(DropWhileOperation::of($this->emitter)($predicate));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
     */
    public function take(int $length): self
    {
        return $this->fork(TakeOperation::of($this->emitter)($length));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
     */
    public function drop(int $length): self
    {
        return $this->fork(DropOperation::of($this->emitter)($length));
    }

    /**
     * @inheritDoc
     * @param callable(TValue): void $callback
     * @psalm-return self<TValue>
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
     * @template TValueI
     * @param TValueI $separator
     * @psalm-return self<TValue|TValueI>
     */
    public function intersperse(mixed $separator): self
    {
        return $this->fork(IntersperseOperation::of($this->emitter)($separator));
    }

    /**
     * @inheritDoc
     * @psalm-return self<TValue>
     */
    public function lines(): self
    {
        return $this->fork(TapOperation::of($this->emitter)(function ($elem) {
            print_r($elem) . PHP_EOL;
        }));
    }

    /**
     * @inheritDoc
     * @template TValueI
     * @param iterable<TValueI> $that
     * @return self<TValue|TValueI>
     */
    public function interleave(iterable $that): self
    {
        return $this->fork(InterleaveOperation::of($this->emitter)($that));
    }

    /**
     * @inheritDoc
     * @template TValueI
     * @param iterable<TValueI> $that
     * @return self<array{TValue, TValueI}>
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

        return $this->fork(MapValuesOperation::of($chunks)(function (array $chunk) {
            return new ArrayList($chunk);
        }));
    }

    /**
     * @inheritDoc
     * @template D
     * @param callable(TValue): D $discriminator
     * @return Stream<array{D, Seq<TValue>}>
     */
    public function groupAdjacentBy(callable $discriminator): Stream
    {
        $adjacent = GroupAdjacentByOperationOperation::of($this->emitter)($discriminator);

        return $this->fork(MapValuesOperation::of($adjacent)(function (array $pair) {
            $pair[1] = new ArrayList($pair[1]);
            return $pair;
        }));
    }

    /**
     * @inheritDoc
     * @psalm-param callable(TValue, TValue): int $cmp
     * @psalm-return self<TValue>
     */
    public function sorted(callable $cmp): self
    {
        return $this->fork(SortedOperation::of($this->emitter)($cmp));
    }
}
