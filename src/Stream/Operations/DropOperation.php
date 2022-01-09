<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Generator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class DropOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @return Generator<TK, TV>
     */
    public function __invoke(int $length): Generator
    {
        return (function () use ($length) {
            $i = 0;

            foreach ($this->gen as $key => $value) {
                if ($i < $length) {
                    $i++;
                    continue;
                }

                yield $key => $value;
            }
        })();
    }
}
