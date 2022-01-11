<?php

declare(strict_types=1);

namespace Whsv26\Functional\Collection\Psalm\CollectionFilterMethodRefinement;

use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Whsv26\Functional\Collection\Collection;
use Whsv26\Functional\Collection\Immutable\Seq\ArrayList;
use Whsv26\Functional\Collection\Map;
use Whsv26\Functional\Collection\NonEmptyMap;
use Whsv26\Functional\Core\Option;
use Whsv26\Functional\Stream\Stream;
use Psalm\Type\Union;

final class RefineTargetExtractor
{
    /**
     * @param list<Union> $templates
     * @return Option<RefinementTarget>
     */
    public static function extract(string $class, string $method, array $templates): Option
    {
        return self::whenFilteringMapKeys($class, $method, $templates)
            ->orElse(fn() => self::whenFilteringMapValues($class, $method, $templates))
            ->orElse(fn() => self::whenFilteringStreamKeys($class, $method, $templates))
            ->orElse(fn() => self::whenFilteringStreamValues($class, $method, $templates))
            ->orElse(fn() => self::whenSimpleFiltering($class, $method, $templates));
    }

    /**
     * @param list<Union> $templates
     * @return Option<RefinementTarget>
     */
    private static function whenFilteringMapKeys(string $class, string $method, array $templates): Option
    {
        return Option::do(function () use ($templates, $method, $class) {
            yield Option::some($method)->filter(fn($m) => $m === strtolower('filterKeys'));
            yield Option::some($class)
                ->filter(function ($c) {
                    return is_a($c, Map::class, true)
                        || is_a($c, NonEmptyMap::class, true);
                });

            return new RefinementTarget(
                target: $templates[0],
                substitute: fn($substitution) => new Union([new TGenericObject(
                    str_replace('NonEmpty', '', $class),
                    [
                        $substitution,
                        $templates[1]
                    ]
                )])
            );
        });
    }

    /**
     * @param list<Union> $templates
     * @return Option<RefinementTarget>
     */
    private static function whenFilteringMapValues(string $class, string $method, array $templates): Option
    {
        return Option::do(function () use ($templates, $method, $class) {
            yield Option::some($method)->filter(fn($m) => $m === strtolower('filterValues'));
            yield Option::some($class)
                ->filter(function ($c) {
                    return is_a($c, Map::class, true)
                        || is_a($c, NonEmptyMap::class, true);
                });

            return new RefinementTarget(
                target: $templates[1],
                substitute: fn($substitution) => new Union([
                    new TGenericObject(
                        str_replace('NonEmpty', '', $class),
                        [
                            $templates[0],
                            $substitution
                        ]
                    )
                ])
            );
        });
    }

    /**
     * @param list<Union> $templates
     * @return Option<RefinementTarget>
     */
    private static function whenFilteringStreamKeys(string $class, string $method, array $templates): Option
    {
        return Option::do(function () use ($templates, $method, $class) {
            yield Option::some($class)->filter(fn($c) => is_a($c, Stream::class, true));
            yield Option::some($method)->filter(fn($m) => $m === strtolower('filterKeys'));

            $pair_types = yield Option::some($templates[0])
                ->map(fn(Union $pair) => $pair->getSingleAtomic())
                ->filterOf(TKeyedArray::class)
                ->map(fn(TKeyedArray $keyed) => array_values($keyed->properties))
                ->map(fn(array $ts) => new ArrayList($ts));

            $pair_key = yield $pair_types->head();
            $pair_value = yield $pair_types->drop(1)->head();

            return new RefinementTarget(
                target: $pair_key,
                substitute: function ($substitution) use ($class, $pair_value) {
                    $pair = new TKeyedArray([$substitution, $pair_value]);
                    $pair->is_list = true;

                    return new Union([
                        new TGenericObject(
                            str_replace('NonEmpty', '', $class),
                            [new Union([$pair])]
                        )
                    ]);
                }
            );
        });
    }

    /**
     * @param list<Union> $templates
     * @return Option<RefinementTarget>
     */
    private static function whenFilteringStreamValues(string $class, string $method, array $templates): Option
    {
        return Option::do(function () use ($templates, $method, $class) {
            yield Option::some($class)->filter(fn($c) => is_a($c, Stream::class, true));
            yield Option::some($method)->filter(fn($m) => $m === strtolower('filterValues'));

            $pair_types = yield Option::some($templates[0])
                ->map(fn(Union $pair) => $pair->getSingleAtomic())
                ->filterOf(TKeyedArray::class)
                ->map(fn(TKeyedArray $keyed) => array_values($keyed->properties))
                ->map(fn(array $ts) => new ArrayList($ts));

            $pair_key = yield $pair_types->head();
            $pair_value = yield $pair_types->drop(1)->head();

            return new RefinementTarget(
                target: $pair_value,
                substitute: function ($substitution) use ($class, $pair_key) {
                    $pair = new TKeyedArray([$pair_key, $substitution]);
                    $pair->is_list = true;

                    return new Union([
                        new TGenericObject(
                            str_replace('NonEmpty', '', $class),
                            [new Union([$pair])]
                        )
                    ]);
                }
            );
        });
    }

    /**
     * @param list<Union> $templates
     * @return Option<RefinementTarget>
     */
    private static function whenSimpleFiltering(string $class, string $method, array $templates): Option
    {
        return Option::do(function () use ($class, $templates, $method) {
            yield Option::some($class)->filter(fn($c) => is_a($c, Collection::class, true));
            yield Option::some($method)->filter(fn($m) => in_array($m, [
                'filter',
                strtolower('filterNotNull'),
            ], true));

            return new RefinementTarget(
                target: $templates[0],
                substitute: fn($substitution) => new Union([
                    new TGenericObject(
                        str_replace('NonEmpty', '', $class),
                        [$substitution]
                    )
                ])
            );
        });
    }
}
