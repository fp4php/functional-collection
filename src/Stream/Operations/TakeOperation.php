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
class TakeOperation extends AbstractStreamOperation
{
    /**
     * @return Generator<TValue>
     */
    public function __invoke(int $length): Generator
    {
        return (function () use ($length) {
            $i = 0;

            foreach ($this->gen as $key => $value) {
                if ($i === $length) {
                    break;
                }

                yield $key => $value;
                $i++;
            }
        })();
    }
}
