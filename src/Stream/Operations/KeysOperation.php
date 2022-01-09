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
class KeysOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @return Generator<int, TK>
     */
    public function __invoke(): Generator
    {
        return (function () {
            foreach ($this->gen as $key => $value) {
                yield $key;
            }
        })();
    }
}
