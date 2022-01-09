<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Static\Stream;

use Whsv26\Functional\Stream\Stream;

final class StreamStaticTest
{
    /**
     * @psalm-param int $input
     * @psalm-return Stream<int>
     */
    public function testEmit(mixed $input): mixed
    {
        return Stream::emit($input);
    }

    /**
     * @psalm-param array{1, 2, 'a'} $input
     * @psalm-return Stream<1|2|'a'>
     */
    public function testEmits(mixed $input): mixed
    {
        return Stream::emits($input);
    }
}
