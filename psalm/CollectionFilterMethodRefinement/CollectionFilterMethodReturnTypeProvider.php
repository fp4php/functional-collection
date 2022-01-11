<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Psalm\CollectionFilterMethodRefinement;

use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;
use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Immutable\Map\HashMap;
use Whsv26\Functional\Collection\Immutable\Set\HashSet;
use Whsv26\Functional\Collection\Immutable\Seq\LinkedList;
use Whsv26\Functional\Collection\Map;
use Whsv26\Functional\Collection\Seq;
use Whsv26\Functional\Collection\Set;
use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Stream;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Node\Expr\VirtualArrowFunction;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type\Union;

final class CollectionFilterMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [
            HashMap::class,
            LinkedList::class,
            ArrayList::class,
            HashSet::class,
            Seq::class,
            Set::class,
            Map::class,
            Stream::class,
        ];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return Option::do(function() use ($event) {
            $source = yield Option::some($event->getSource())->filterOf(StatementsAnalyzer::class);

            $refinement_target = yield RefineTargetExtractor::extract(
                $event->getFqClasslikeName(),
                $event->getMethodNameLowercase(),
                array_values($event->getTemplateTypeParameters() ?? [])
            );

            $predicate = yield self::extractPredicateArg($event)
                ->map(fn(Arg $arg) => $arg->value)
                ->filter(fn(Expr $expr) => $expr instanceof FunctionLike);

            $refinement_context = new RefinementContext(
                predicate: $predicate,
                execution_context: $event->getContext(),
                codebase: $source->getCodebase(),
                source: $source,
            );

            $refined = RefineByPredicate::refine(
                $refinement_context,
                $refinement_target,
            );

            return ($refinement_target->substitute)($refined);

        })->get();
    }

    /**
     * @psalm-return Option<Arg>
     */
    private static function extractPredicateArg(MethodReturnTypeProviderEvent $event): Option
    {
        return ArrayList::collect($event->getCallArgs())
            ->head()
            ->orElse(fn() => self::mockNotNullPredicateArg($event));
    }

    /**
     * @psalm-return Option<Arg>
     */
    private static function mockNotNullPredicateArg(MethodReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            yield Option::some($event->getMethodNameLowercase())
                ->filter(fn($method) => strtolower('filterNotNull') === $method);

            $var = new Variable('$elem');
            $expr = new Isset_([$var]);
            $param = new Param($var);

            $expr = new VirtualArrowFunction([
                'expr' => $expr,
                'params' => [$param],
            ]);

            return new Arg($expr);
        });
    }
}
