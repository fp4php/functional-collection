<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Psalm\CollectionFilterMethodRefinement;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Return_;
use Psalm\CodeLocation;
use Psalm\Internal\Algebra;
use Psalm\Internal\Algebra\FormulaGenerator;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Type\Reconciler;
use Psalm\Type\Union;
use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Map;
use Whsv26\Functional\Collection\NonEmptyMap;
use Whsv26\Functional\Core\Option;

use function spl_object_id;
use function str_starts_with;

/**
 * @psalm-type CollectionTypeParameters = array{Union, Union}
 * @psalm-type PsalmAssertions = array<string, array<array<int, string>>>
 */
final class RefineByPredicate
{
    private const CONSTANT_ARG_NAME = '$constant_arg_name';

    /**
     * Refine collection type-parameters
     * By predicate expression
     *
     * @psalm-return RefinementResult
     */
    public static function for(RefinementContext $context, CollectionTypeParams $collection_params): RefinementResult
    {
        return new RefinementResult(
            self::forKey($context, $collection_params)->getOrElse($collection_params->key_type),
            self::forValue($context, $collection_params)->getOrElse($collection_params->val_type),
        );
    }

    /**
     * Try to refine collection key type-parameter
     *
     * @psalm-return Option<Union>
     */
    public static function forKey(RefinementContext $context, CollectionTypeParams $collection_params): Option
    {
        return Option::do(function() use ($context, $collection_params) {
            $predicate_key_arg_name = yield self::getPredicateKeyArgumentName($context);
            $predicate_return_expr  = yield self::getPredicateSingleReturn($context);

            $assertions_for_collection_key = self::collectAssertions(
                context: $context,
                return_expr: $predicate_return_expr,
                predicate_arg_name: $predicate_key_arg_name,
            );

            return yield self::reconcile(
                source: $context->source,
                assertions: $assertions_for_collection_key,
                collection_type_param: $collection_params->key_type,
                return_expr: $predicate_return_expr,
            );
        });
    }

    /**
     * Try to refine collection value type-parameter
     *
     * @psalm-return Option<Union>
     */
    public static function forValue(RefinementContext $context, CollectionTypeParams $collection_params): Option
    {
        return Option::do(function() use ($context, $collection_params) {
            $predicate_value_arg_name = yield self::getPredicateValueArgumentName($context);
            $predicate_return_expr    = yield self::getPredicateSingleReturn($context);

            $assertions_for_collection_value = self::collectAssertions(
                context: $context,
                return_expr: $predicate_return_expr,
                predicate_arg_name: $predicate_value_arg_name,
            );

            return yield self::reconcile(
                source: $context->source,
                assertions: $assertions_for_collection_value,
                collection_type_param: $collection_params->val_type,
                return_expr: $predicate_return_expr,
            );
        });
    }

    /**
     * Returns key argument name of $predicate that going to be refined.
     *
     * @psalm-return Option<non-empty-string>
     */
    private static function getPredicateKeyArgumentName(RefinementContext $context): Option
    {
        $refine_for_map = is_a($context->refine_for, Map::class, true)
            || is_a($context->refine_for, NonEmptyMap::class, true);

        $key_arg = $refine_for_map
            ? $context->predicate->getParams()[0] ?? null
            : $context->predicate->getParams()[1] ?? null;

        return Option::fromNullable($key_arg)
            ->map(fn($param) => $param->var)
            ->filterOf(Variable::class)
            ->map(fn(Variable $var) => $var->name)
            ->filter(fn(string|Expr $name) => is_string($name))
            ->map(fn($name) => $refine_for_map
                ? ('$' . $name . '->key')
                : ('$' . $name));
    }

    /**
     * Returns value argument name of $predicate that going to be refined.
     *
     * @psalm-return Option<non-empty-string>
     */
    private static function getPredicateValueArgumentName(RefinementContext $context): Option
    {
        $refine_for_map = is_a($context->refine_for, Map::class, true)
            || is_a($context->refine_for, NonEmptyMap::class, true);

        return ArrayList::collect($context->predicate->getParams())
            ->head()
            ->map(fn($param) => $param->var)
            ->filterOf(Variable::class)
            ->map(fn(Variable $var) => $var->name)
            ->filter(fn(string|Expr $name) => is_string($name))
            ->map(fn($name) => $refine_for_map
                ? ('$' . $name . '->value')
                : ('$' . $name));
    }

    /**
     * Returns single return expression of $predicate if present.
     * Collection type parameter can be refined only for function with single return.
     *
     * @psalm-return Option<Expr>
     */
    private static function getPredicateSingleReturn(RefinementContext $context): Option
    {
        return ArrayList::collect($context->predicate->getStmts() ?? [])
            ->firstOf(Return_::class)
            ->flatMap(fn($return_statement) => Option::fromNullable($return_statement->expr));
    }

    /**
     * Collects assertion for $predicate_arg_name from $return_expr.
     *
     * @psalm-return PsalmAssertions
     */
    private static function collectAssertions(
        RefinementContext $context,
        Expr $return_expr,
        string $predicate_arg_name,
    ): array
    {
        $cond_object_id = spl_object_id($return_expr);

        // Generate formula
        // Which is list of clauses (possibilities and impossibilities)
        // From conditional filter expression
        $filter_clauses = FormulaGenerator::getFormula(
            conditional_object_id: $cond_object_id,
            creating_object_id: $cond_object_id,
            conditional: $return_expr,
            this_class_name: $context->execution_context->self,
            source: $context->source,
            codebase: $context->codebase
        );

        $assertions = [];

        // Extract truths from list of clauses
        // Which are clauses with only one possible value
        $truths = Algebra::getTruthsFromFormula($filter_clauses, $cond_object_id);

        foreach ($truths as $key => $assertion) {
            if (!str_starts_with($key, $predicate_arg_name)) {
                continue;
            }

            // Replace arg name with constant name
            $arn_name = str_replace($predicate_arg_name, self::CONSTANT_ARG_NAME, $key);

            $assertions[$arn_name] = $assertion;
        }

        return $assertions;
    }

    /**
     * Reconciles $collection_type_param with $assertions using internal Psalm api.
     *
     * @psalm-param PsalmAssertions $assertions
     * @psalm-return Option<Union>
     */
    private static function reconcile(
        StatementsAnalyzer $source,
        array $assertions,
        Union $collection_type_param,
        Expr $return_expr
    ): Option
    {
        return Option::do(function() use ($source, $assertions, $collection_type_param, $return_expr) {
            yield Option::some($assertions)
                ->filter(fn($as) => count($as) > 0);

            // reconcileKeyedTypes takes it by ref
            $changed_var_ids = [];

            $reconciled_types = Reconciler::reconcileKeyedTypes(
                new_types: $assertions,
                active_new_types: $assertions,
                existing_types: [self::CONSTANT_ARG_NAME => $collection_type_param],
                changed_var_ids: $changed_var_ids,
                referenced_var_ids: [self::CONSTANT_ARG_NAME => true],
                statements_analyzer: $source,
                template_type_map: $source->getTemplateTypeMap() ?: [],
                inside_loop: false,
                code_location: new CodeLocation($source, $return_expr)
            );

            return yield Option::fromNullable($reconciled_types[self::CONSTANT_ARG_NAME] ?? null);
        });
    }
}
