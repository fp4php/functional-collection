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
class AppendedOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TVI
     * @psalm-param TVI $elem
     * @return Generator<TV|TVI>
     */
    public function __invoke(mixed $elem): Generator
    {
        return (function () use ($elem) {
            foreach ($this->gen as $prefixElem) {
                yield $prefixElem;
            }

            yield $elem;
        })();
    }
}
