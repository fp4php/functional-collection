<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Psalm\CollectionFilterMethodRefinement;

use Closure;
use Psalm\Type\Union;

/**
 * @psalm-immutable
 */
final class RefinementTarget
{
    /**
     * @param Closure(Union): Union $substitute
     */
    public function __construct(
        public Union $target,
        public Closure $substitute,
    ) { }
}
