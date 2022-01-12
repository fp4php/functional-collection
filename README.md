![psalm level](https://shepherd.dev/github/whsv26/functional-collection/level.svg)
![psalm type coverage](https://shepherd.dev/github/whsv26/functional-collection/coverage.svg)
[![phpunit coverage](https://coveralls.io/repos/github/whsv26/functional-collection/badge.svg)](https://coveralls.io/github/whsv26/functional-collection)

---

## Installation

### Composer 

```console
composer require whsv26/functional-collection
```

### Enable psalm plugin (optional)
To improve type inference

```console
vendor/bin/psalm-plugin enable Whsv26\\Functional\\Collection\\Psalm\\Plugin
```

# Collections
## Hierarchy

```php
Collection<TV> -> Seq<TV>     -> LinkedList<TV>
Collection<TV> -> Seq<TV>     -> ArrayList<TV>
Collection<TV> -> Set<TV>     -> HashSet<TV>
Collection<TV> -> Map<TK, TV> -> HashMap<TK, TV>
```


## ArrayList

`Seq<TV>` interface implementation.

Collection with O(1) `Seq::at()` and `Seq::__invoke()` operations.

``` php
$collection = ArrayList::collect([
    new Foo(1), new Foo(2) 
    new Foo(3), new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->filter(fn(int $elem) => $elem > 1)
    ->reduce(fn($acc, $elem) => $acc + $elem)
    ->getOrElse(0); // 9
```

## LinkedList

`Seq<TV>` interface implementation.

Collection with O(1) prepend operation.

``` php
$collection = LinkedList::collect([
    new Foo(1), new Foo(2) 
    new Foo(3), new Foo(4),
]);

$collection
    ->map(fn(Foo $elem) => $elem->a)
    ->filter(fn(int $elem) => $elem > 1)
    ->reduce(fn($acc, $elem) => $acc + $elem)
    ->getOrElse(0); // 9
```

## HashMap

`Map<TK, TV>` interface implementation.

Key-value storage. It's possible to store objects as keys.

Object keys comparison by default uses `spl_object_hash` function. If
you want to override default comparison behaviour then you need to
implement HashContract interface for your classes which objects will be
used as keys in HashMap.

``` php
class Foo implements HashContract
{
    public function __construct(public int $a, public bool $b = true)
    {
    }

    public function equals(mixed $that): bool
    {
        return $that instanceof self
            && $this->a === $that->a
            && $this->b === $that->b;
    }

    public function hashCode(): string
    {
        return md5(implode(',', [$this->a, $this->b]));
    }
}

$hashMap = HashMap::collectPairs([
    [new Foo(1), 1], [new Foo(2), 2],
    [new Foo(3), 3], [new Foo(4), 4]
]);

$hashMap(new Foo(2))->getOrElse(0); // 2
```

## HashSet

`Set<TV>` interface implementation.

Collection of unique elements.

Object comparison by default uses `spl_object_hash` function. If you
want to override default comparison behaviour then you need to implement
HashContract interface for your classes which objects will be used as
elements in HashSet.

``` php
class Foo implements HashContract
{
    public function __construct(public int $a, public bool $b = true)
    {
    }

    public function equals(mixed $that): bool
    {
        return $that instanceof self
            && $this->a === $that->a
            && $this->b === $that->b;
    }

    public function hashCode(): string
    {
        return md5(implode(',', [$this->a, $this->b]));
    }
}

$set = HashSet::collect([
    new Foo(1), new Foo(2), new Foo(2), 
    new Foo(3), new Foo(3), new Foo(4),
]);

$set->map(fn(Foo $elem) => $elem->a)
    ->filter(fn(int $elem) => $elem > 1)
    ->reduce(fn($acc, $cur) => $acc + $cur)
    ->getOrElse(0); // 9

/**
 * Check if set contains given element
 */ 
$set(new Foo(2)); // true

/**
 * Check if one set is contained in another set 
 */
$set->subsetOf(HashSet::collect([
    new Foo(1), new Foo(2), new Foo(3), 
    new Foo(4), new Foo(5), new Foo(6),
])); // true
```

- Easy to move from MANY to ONE for many-to-one relations

<!-- end list -->

``` php
class Ceo
{
    public function __construct(public string $name) { }
}

class Manager
{
    public function __construct(public string $name, public Ceo $ceo) { }
}

class Developer
{
    public function __construct(public string $name, public Manager $manager) { }
}

$ceo = new Ceo('CEO');
$managerX = new Manager('Manager X', $ceo);
$managerY = new Manager('Manager Y', $ceo);
$developerA = new Developer('Developer A', $managerX);
$developerB = new Developer('Developer B', $managerX);
$developerC = new Developer('Developer C', $managerY);

HashSet::collect([$developerA, $developerB, $developerC])
    ->map(fn(Developer $developer) => $developer->manager)
    ->map(fn(Manager $manager) => $manager->ceo)
    ->tap(fn(Ceo $ceo) => print_r($ceo->name . PHP_EOL)); // CEO. Not CEOCEOCEO
```


# Streams

## Overview

Streams are based on generators. They are immutable generator object
wrappers.

Their operations are lazy and will be applied only once when stream
terminal operation like `toList` will be called after stream compilation.

Every non-terminal stream operation will produce new stream fork. No
more than one fork can be made from stream object.

Stream can be created from any iterable. Additionally, there are fabric
static methods.

``` php
Stream::emit(1)
    ->repeat() // [1, 1, ...] infinite stream
    ->map(fn(int $i) => $i + 1) // [2, 2, ...] infinite stream
    ->take(5) // [2, 2, 2, 2, 2]
    ->compile()
    ->toList(); // [2, 2, 2, 2, 2]
```

``` php
Stream::infinite() 
    ->map(fn() => rand(0, 9)) // infinite stream of random digits
    ->intersperse(',') // [x1, ',', x2, ',', ...]
    ->tap(function () {
        // constant memory usage
        echo memory_get_usage(true) . PHP_EOL; 
    })
    ->take(50000) // make infinite stream finite
    ->compile()
    ->fold('', fn(string $acc, $elem) => $acc . $elem); // call terminal operation to run stream
```

``` php
/**
 * @return Option<float>
 */
function safeDiv(int $dividend, int $divisor): Option {
    return Option::condLazy(0 !== $divisor, fn() => $dividend / $divisor);
}

Stream::emits([0, 2])
    ->repeatN(3) // [0, 2, 0, 2, 0, 2]
    ->filterMap(fn(int $i) => safeDiv($i, $i))  // [1, 1, 1]
    ->take(9999) // [1, 1, 1]
    ->compile()
    ->toFile('/dev/null');
```

``` php
/**
 * Several streams may be interleaved together
 * It's zip + flatMap combination 
 */

Stream::emits([1, 2, 3])
    ->interleave(Stream::emits([4, 5, 6, 7])) // [1, 4, 2, 5, 3, 6]
    ->intersperse('+') // [1, '+', 4, '+', 2, '+', 5, '+', 3, '+', 6]
    ->compile()
    ->fold('', fn(string $acc, $cur) => $acc . $cur) // '1+4+2+5+3+6'
```

## Bulk insert into multiple tables

``` php
Stream::emits($iterableDatabaseCursor)
    ->chunks(5000)
    // Insert chunks of 5000 rows to 'events' table
    ->tap(fn(Seq $chunk) => $client->insert('events', $chunk))
    ->flatMap(function(Seq $chunk) {
        return $chunk->filter(fn(Event $event) => $event->type === 'SOME_TYPE')
    })
    ->chunks(5000)
    // Insert chunks of 5000 rows to 'events_of_some_type' table
    ->tap(fn(Seq $chunk) => $client->insert('events_of_some_type', $chunk))
    ->compile()
    ->drain();
```

## JSON Lines example

``` php
class Foo
{
    public function __construct(public int $a, public bool $b = true, public bool $c = true) { }
}

function generateJsonLinesFile(string $path): void
{
    Stream::infinite()
        ->map(fn() => new Foo(rand(), 1 === rand(0, 1), 1 === rand(0, 1)))
        ->map(fn(Foo $foo) => json_encode([$foo->a, $foo->b, $foo->c]))
        ->prepended(json_encode(["a", "b", "c"]))
        ->take(10000)
        ->intersperse(PHP_EOL)
        ->compile()
        ->toFile($path);
}

/**
 * @return list<Foo>
 */
function parseJsonLinesFile(string $path): array
{
    $chars = function () use ($path): Generator {
        $file = new SplFileObject($path);
        
        while(false !== ($char = $file->fgetc())) {
            yield $char;
        }
        
        $file = null;
    };

    return Stream::emits($chars())
        ->groupAdjacentBy(fn($char) => PHP_EOL === $char)
        ->map(fn(array $pair) => $pair[1])
        ->map(fn(Seq $line) => $line->mkString(sep: ''))
        ->filterMap(parseFoo(...))
        ->compile()
        ->toList();
}

/**
 * @return Option<Foo>
 */
function parseFoo(string $json): Option
{
    return jsonDecode($json)
        ->toOption()
        ->filter(fn($candidate) => is_array($candidate))
        ->filter(fn($candidate) => array_key_exists(0, $candidate) && is_int($candidate[0]))
        ->filter(fn($candidate) => array_key_exists(1, $candidate) && is_bool($candidate[1]))
        ->filter(fn($candidate) => array_key_exists(2, $candidate) && is_bool($candidate[2]))
        ->map(fn($tuple) => new Foo($tuple[0], $tuple[1], $tuple[2]));
}

generateJsonLinesFile('out.jsonl');
parseJsonLinesFile('out.jsonl');
```
