<?php

declare(strict_types=1);

namespace Whsv26\Functional\Stream\Operations;

use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\AbstractStreamOperation;
use Whsv26\Functional\Stream\Stream;

/**
 * @template TValue
 * @psalm-immutable
 * @extends AbstractStreamOperation<TValue>
 */
class EveryMapOperation extends AbstractStreamOperation
{
    /**
     * @template TValueIn
     * @param callable(TValue): Option<TValueIn> $f
     * @psalm-return Option<Stream<TValueIn>>
     */
    public function __invoke(callable $f, ?string $class = null): Option
    {
        $mapped = [];

        foreach ($this->gen as $value) {
            $option = $f($value);

            if ($option->isNone()) {
                return Option::none();
            }

            $mapped[] = $option->get();
        }

        return Option::some(Stream::emits($mapped));
    }
}
