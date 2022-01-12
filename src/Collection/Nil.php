<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

/**
 * @psalm-immutable
 * @extends LinkedList<empty>
 */
final class Nil extends LinkedList
{
    private static ?self $instance = null;

    /**
     * @psalm-pure
     */
    public static function getInstance(): self
    {
        return is_null(self::$instance) ? self::$instance = new self() : self::$instance;
    }
}
