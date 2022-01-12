<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Set;

use PHPUnit\Framework\TestCase;
use Whsv26\Functional\Collection\Set\HashSet;

final class SetTest extends TestCase
{
    public function testCasts(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toList(),
        );

        $this->assertEquals(
            [1],
            HashSet::singleton(1)->toList(),
        );

        $this->assertEquals(
            [],
            HashSet::empty()->toList(),
        );

        $this->assertTrue(HashSet::empty()->isEmpty());
        $this->assertFalse(HashSet::empty()->isNonEmpty());
    }

    public function testCount(): void
    {
        $this->assertCount(3, HashSet::collect([1, 2, 3]));
    }
}
