<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TKey
 * @template TValue
 * @psalm-immutable
 * @extends AbstractOperation<TKey, TValue>
 */
class ChunksOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-template TPreserve of bool
     * @psalm-param TPreserve $preserveKeys
     * @psalm-param positive-int $size
     * @psalm-return (TPreserve is true
     *     ? Generator<int, non-empty-array<TKey, TValue>>
     *     : Generator<int, non-empty-list<TValue>>
     * )
     */
    public function __invoke(int $size, bool $preserveKeys = false): Generator
    {
        return (function () use ($preserveKeys, $size) {
            $chunk = [];
            $i = 0;

            foreach ($this->gen as $key => $value) {
                $i++;

                if ($preserveKeys) {
                    $chunk[$key] = $value;
                } else {
                    $chunk[] = $value;
                }

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
