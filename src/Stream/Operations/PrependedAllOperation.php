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
class PrependedAllOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TVI
     * @psalm-param iterable<TVI> $prefix
     * @return Generator<TV|TVI>
     */
    public function __invoke(iterable $prefix): Generator
    {
        return (function () use ($prefix) {
            foreach ($prefix as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($this->gen as $suffixElem) {
                yield $suffixElem;
            }
        })();
    }
}
