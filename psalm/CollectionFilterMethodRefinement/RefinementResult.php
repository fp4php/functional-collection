<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Psalm\CollectionFilterMethodRefinement;

use Psalm\Type\Union;

/**
 * @psalm-immutable
 */
final class RefinementResult
{
    public function __construct(
        public Union $collection_key_type,
        public Union $collection_value_type,
    ) { }
}
