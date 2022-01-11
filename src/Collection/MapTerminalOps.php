<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection;

use Whsv26\Functional\Core\Option;

/**
 * @template TKey
 * @template-covariant TValue
 * @psalm-immutable
 */
interface MapTerminalOps
{
    /**
     * Get an element by its key
     * Alias for {@see MapOps::get}
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])('b')->getOrElse(0);
     * => 2
     *
     * >>> HashMap::collect(['a' => 1, 'b' => 2])('c')->getOrElse(0);
     * => 0
     * ```
     *
     * @param TKey $key
     * @return Option<TValue>
     */
    public function __invoke(mixed $key): Option;

    /**
     * Get an element by its key
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->get('b')->getOrElse(0);
     * => 2
     *
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->get('c')->getOrElse(0);
     * => 0
     * ```
     *
     * @param TKey $key
     * @return Option<TValue>
     */
    public function get(mixed $key): Option;

    /**
     * Check if at least one collection element is present
     */
    public function isNonEmpty(): bool;

    /**
     * Check if there are no elements in collection
     */
    public function isEmpty(): bool;
}
