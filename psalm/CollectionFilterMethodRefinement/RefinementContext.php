<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Psalm\CollectionFilterMethodRefinement;

use PhpParser\Node\FunctionLike;
use Psalm\Codebase;
use Psalm\Context;
use Psalm\Internal\Analyzer\StatementsAnalyzer;

/**
 * @psalm-immutable
 */
final class RefinementContext
{
    public function __construct(
        public FunctionLike $predicate,
        public Context $execution_context,
        public Codebase $codebase,
        public StatementsAnalyzer $source,
    ) { }
}
