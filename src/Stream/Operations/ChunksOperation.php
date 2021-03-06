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
class ChunksOperation extends AbstractStreamOperation
{
    /**
     * @param positive-int $size
     * @return Generator<int, non-empty-list<TValue>>
     */
    public function __invoke(int $size): Generator
    {
        return (function () use ($size) {
            $chunk = [];
            $i = 0;

            foreach ($this->gen as $value) {
                $i++;

                $chunk[] = $value;

                if (0 === $i % $size) {
                    yield $chunk;
                    $chunk = [];
                }
            }

            if (!empty($chunk)) {
                yield $chunk;
            }
        })();
    }
}
