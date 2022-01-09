<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Stream;

use Whsv26\Functional\Core\Unit;
use Whsv26\Functional\Stream\Stream;
use PHPUnit\Framework\TestCase;

final class StreamEmitterTest extends TestCase
{
    public function testAwake(): void
    {
        $this->assertEquals([0], Stream::awakeEvery(0)->take(1)->compile()->toList());
    }

    public function testConstant(): void
    {
        $this->assertEquals([1, 1], Stream::constant(1)->take(2)->compile()->toList());
    }

    public function testInfinite(): void
    {
        $this->assertEquals([Unit::getInstance(), Unit::getInstance()], Stream::infinite()->take(2)->compile()->toList());
    }

    public function testRange(): void
    {
        $this->assertEquals([0, 1], Stream::range(0, 2)->compile()->toList());
        $this->assertEquals([0, 2, 4], Stream::range(0, 5, 2)->compile()->toList());
    }
}
