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
class TailOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @return Generator<TK, TV>
     */
    public function __invoke(): Generator
    {
        return (function () {
            $isFirst = true;

            foreach ($this->gen as $key => $value) {
                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }

                yield $key => $value;
            }
        })();
    }
}
