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
use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyArrayList;
use Whsv26\Functional\Collection\Immutable\NonEmptyMap\NonEmptyHashMap;
use Whsv26\Functional\Collection\Immutable\NonEmptySet\NonEmptyHashSet;
use Whsv26\Functional\Collection\Immutable\NonEmptySeq\NonEmptyLinkedList;
use Whsv26\Functional\Collection\NonEmptyMap;
use Whsv26\Functional\Collection\NonEmptySeq;
use Whsv26\Functional\Collection\NonEmptySet;
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
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

final class CollectionFilterMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getMethodNames(): array
    {
        return [
            'filter',
            strtolower('filterNotNull'),
        ];
    }

    public static function getClassLikeNames(): array
    {
        return [
            HashMap::class,
            NonEmptyHashMap::class,
            LinkedList::class,
            NonEmptyLinkedList::class,
            ArrayList::class,
            NonEmptyArrayList::class,
            HashSet::class,
            NonEmptyHashSet::class,
            Seq::class,
            NonEmptySeq::class,
            Set::class,
            NonEmptySet::class,
            Map::class,
            NonEmptyMap::class,
            Stream::class,
        ];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        $reconciled = Option::do(function() use ($event) {
            yield Option::some($event->getMethodNameLowercase())
                ->filter(fn($method) => in_array($method, self::getMethodNames()));

            $source = yield Option::some($event->getSource())->filterOf(StatementsAnalyzer::class);

            $predicate = yield self::extractPredicateArg($event)
                ->map(fn(Arg $arg) => $arg->value)
                ->filter(fn(Expr $expr) => $expr instanceof FunctionLike);

            $template_params = yield Option::fromNullable($event->getTemplateTypeParameters());

            $collection_type_params = 2 === count($template_params)
                ? new CollectionTypeParams($template_params[0], $template_params[1])
                : new CollectionTypeParams(Type::getArrayKey(), $template_params[0]);

            $refinement_context = new RefinementContext(
                refine_for: $event->getFqClasslikeName(),
                predicate: $predicate,
                execution_context: $event->getContext(),
                codebase: $source->getCodebase(),
                source: $source,
            );

            $result = RefineByPredicate::for(
                $refinement_context,
                $collection_type_params,
            );

            return yield self::getReturnType($event, $result);
        });

        return $reconciled->get();
    }

    /**
     * @psalm-return Option<Arg>
     */
    public static function extractPredicateArg(MethodReturnTypeProviderEvent $event): Option
    {
        return ArrayList::collect($event->getCallArgs())
            ->head()
            ->orElse(fn() => self::mockNotNullPredicateArg($event));
    }

    /**
     * @psalm-return Option<Arg>
     */
    public static function mockNotNullPredicateArg(MethodReturnTypeProviderEvent $event): Option
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

    /**
     * @psalm-return Option<Union>
     */
    private static function getReturnType(MethodReturnTypeProviderEvent $event, RefinementResult $result): Option
    {
        $class_name = str_replace('NonEmpty', '', $event->getFqClasslikeName());

        $template_params = is_a($class_name, Map::class, true) || is_a($class_name, NonEmptyMap::class, true)
            ? [$result->collection_key_type, $result->collection_value_type]
            : [$result->collection_value_type];

        return Option::some(new Union([
            new TGenericObject($class_name, $template_params),
        ]));
    }
}
