<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Psalm\Suppress;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\StatementsSource;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;
use Whsv26\Functional\Collection\ArrayList;
use Whsv26\Functional\Collection\HashMap;
use Whsv26\Functional\Collection\HashSet;
use Whsv26\Functional\Collection\LinkedList;
use Whsv26\Functional\Collection\Set;
use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Stream;

/**
 * @psalm-type CallInfo = array{
 *     call_name: string,
 *     call_node: FuncCall|MethodCall,
 * }
 */
final class UnusedCallSuppressor implements AfterExpressionAnalysisInterface
{
    /**
     * @return Set<string>
     */
    public static function getWhiteList(): Set
    {
        static $set = null;

        if (is_null($set)) {
            $set = HashSet::collect([
                LinkedList::class.'::tap',
                ArrayList::class.'::tap',
                HashSet::class.'::tap',
                HashMap::class.'::tap',
                Stream::class.'::tap',
                Stream::class.'::toFile',
                Stream::class.'::drain',
            ]);
        }

        /** @var Set<string> */
        return $set;
    }

    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        Option::do(function() use ($event) {
            $call_info = yield self::getCallInfo($event->getExpr(), $event->getStatementsSource())
                ->filter(fn($info) => self::getWhiteList()($info['call_name']));

            self::removeUnusedMethodCallIssue($event->getStatementsSource(), $call_info['call_node']);
        });

        return null;
    }

    private static function removeUnusedMethodCallIssue(StatementsSource $source, FuncCall|MethodCall $call): void
    {
        Option::do(function() use ($source, $call) {
            $optional_pos = Option::some(match (true) {
                $call instanceof FuncCall => $call->getAttribute('startFilePos'),
                $call instanceof MethodCall => $call->name->getAttribute('startFilePos'),
            });

            $start_func_call_pos = yield $optional_pos
                ->filter(fn($pos) => is_int($pos));

            $pathname = $source->getFilePath();
            $issues = IssueBuffer::getIssuesData();

            if (!array_key_exists($pathname, $issues)) {
                return;
            }

            $issue_type = match (true) {
                $call instanceof FuncCall => 'UnusedFunctionCall',
                $call instanceof MethodCall => 'UnusedMethodCall',
            };

            IssueBuffer::remove($pathname, $issue_type, $start_func_call_pos);
        });
    }

    /**
     * @return Option<CallInfo>
     */
    public static function getCallInfo(Expr $expr, StatementsSource $source): Option
    {
        return self::getFunctionCallInfo($expr)
            ->orElse(fn() => self::getMethodCallInfo($expr, $source));
    }

    /**
     * @return Option<CallInfo>
     */
    public static function getFunctionCallInfo(Expr $expr): Option
    {
        return Option::do(function() use ($expr) {
            $func_call = yield Option::some($expr)->filterOf(FuncCall::class);
            $func_name = yield Option::some($func_call->name)
                ->filterOf(Name::class)
                ->map(fn(Name $name): mixed => $name->getAttribute('resolvedName'))
                ->filter(fn($name) => is_string($name));

            return [
                'call_name' => $func_name,
                'call_node' => $func_call,
            ];
        });
    }

    /**
     * @return Option<CallInfo>
     */
    public static function getMethodCallInfo(Expr $expr, StatementsSource $source): Option
    {
        return Option::do(function() use ($expr, $source) {
            $method_call = yield Option::some($expr)->filterOf(MethodCall::class);

            $method_name = yield Option::some($method_call->name)
                ->filterOf(Identifier::class)
                ->map(fn(Identifier $id) => $id->name);


            $var_type = yield Option::fromNullable($source->getNodeTypeProvider()->getType($method_call->var))
                ->map(fn(Union $union) => array_values($union->getAtomicTypes()))
                ->filter(fn(array $atomics) => 1 === count($atomics))
                ->flatMap(fn(array $atomics) => Option::some($atomics[0])->filterOf(TGenericObject::class));

            return [
                'call_name' => "{$var_type->value}::{$method_name}",
                'call_node' => $method_call,
            ];
        });
    }
}
