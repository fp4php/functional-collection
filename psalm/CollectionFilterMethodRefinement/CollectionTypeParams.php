<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Psalm\CollectionFilterMethodRefinement;

use Psalm\Type\Union;

final class CollectionTypeParams
{
    public function __construct(
        public Union $key_type,
        public Union $val_type,
    ) { }
}
