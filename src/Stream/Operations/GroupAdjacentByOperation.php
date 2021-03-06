<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;
use Whsv26\Functional\Stream\AbstractStreamOperation;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class GroupAdjacentByOperation extends AbstractStreamOperation
{
    /**
     * @template D
     * @param callable(TValue): D $discriminator
     * @return Generator<int, array{D, non-empty-list<TValue>}>
     */
    public function __invoke(callable $discriminator): Generator
    {
        return (function () use ($discriminator) {
            $buffer = [];
            $prevDisc = null;
            $isHead = true;

            foreach ($this->gen as $elem) {
                if ($isHead) {
                    $isHead = false;
                    $prevDisc = $discriminator($elem);
                }

                $curDisc = $discriminator($elem);

                if ($prevDisc !== $curDisc && !empty($buffer)) {
                    yield [$prevDisc, $buffer];
                    $buffer = [];
                }

                $buffer[] = $elem;
                $prevDisc = $curDisc;
            }

            if (!empty($buffer)) {
                yield [$prevDisc, $buffer];
            }
        })();
    }
}
