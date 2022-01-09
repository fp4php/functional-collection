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
class AppendedAllOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @return Generator<TV|TVI>
     */
    public function __invoke(iterable $suffix): Generator
    {
        return (function () use ($suffix) {
            foreach ($this->gen as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($suffix as $suffixElem) {
                yield $suffixElem;
            }
        })();
    }
}
