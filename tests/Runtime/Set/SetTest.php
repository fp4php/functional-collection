<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Tests\Runtime\Set;

use Whsv26\Functional\Collection\Immutable\Set\HashSet;
use PHPUnit\Framework\TestCase;

final class SetTest extends TestCase
{
    public function testCasts(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toList(),
        );
    }

    public function testCount(): void
    {
        $this->assertCount(3, HashSet::collect([1, 2, 3]));
    }
}
