<?php

use Foxbyte\InertiaDataProviders\DataProvider;
use Foxbyte\InertiaDataProviders\DataProviderCollection;
use Illuminate\Support\Collection;

it('constructs with multiple data providers', function () {
    $a = makeSimpleProvider('a', 'value_a');
    $b = makeSimpleProvider('b', 'value_b');

    $collection = new DataProviderCollection($a, $b);

    expect($collection->collection())->toHaveCount(2);
});

it('adds providers with add()', function () {
    $collection = new DataProviderCollection();
    $collection->add(makeSimpleProvider('a', 'value_a'));

    expect($collection->collection())->toHaveCount(1);
});

it('removes providers by class name', function () {
    $a = makeProviderA();
    $b = makeProviderB();

    $collection = new DataProviderCollection($a, $b);
    $collection->remove($a::class);

    expect($collection->collection())->toHaveCount(1);
    expect($collection->toArray())->toHaveKey('b', 'value_b');
});

it('empties all providers', function () {
    $collection = new DataProviderCollection(
        makeSimpleProvider('a', 'value_a'),
    );

    $collection->empty();

    expect($collection->collection())->toHaveCount(0);
    expect($collection->toArray())->toBe([]);
});

it('executes callback when condition is true', function () {
    $collection = new DataProviderCollection();

    $collection->when(true, function (DataProviderCollection $c) {
        $c->add(makeSimpleProvider('added', 'yes'));
    });

    expect($collection->toArray())->toHaveKey('added', 'yes');
});

it('skips callback when condition is false', function () {
    $collection = new DataProviderCollection();

    $collection->when(false, function (DataProviderCollection $c) {
        $c->add(makeSimpleProvider('added', 'yes'));
    });

    expect($collection->toArray())->toBe([]);
});

it('accepts callable condition in when()', function () {
    $collection = new DataProviderCollection();

    $collection->when(fn () => true, function (DataProviderCollection $c) {
        $c->add(makeSimpleProvider('added', 'yes'));
    });

    expect($collection->toArray())->toHaveKey('added', 'yes');
});

it('executes callback when unless condition is false', function () {
    $collection = new DataProviderCollection();

    $collection->unless(false, function (DataProviderCollection $c) {
        $c->add(makeSimpleProvider('added', 'yes'));
    });

    expect($collection->toArray())->toHaveKey('added', 'yes');
});

it('skips callback when unless condition is true', function () {
    $collection = new DataProviderCollection();

    $collection->unless(true, function (DataProviderCollection $c) {
        $c->add(makeSimpleProvider('added', 'yes'));
    });

    expect($collection->toArray())->toBe([]);
});

it('merges all providers in toArray()', function () {
    $a = makeSimpleProvider('a', 'value_a');
    $b = makeSimpleProvider('b', 'value_b');

    $collection = new DataProviderCollection($a, $b);

    expect($collection->toArray())->toBe([
        'a' => 'value_a',
        'b' => 'value_b',
    ]);
});

it('returns the underlying Collection instance', function () {
    $collection = new DataProviderCollection();

    expect($collection->collection())->toBeInstanceOf(Collection::class);
});

// Helper: creates a simple anonymous DataProvider with one static data key
function makeSimpleProvider(string $key, mixed $value): DataProvider
{
    return new class($key, $value) extends DataProvider {
        public function __construct(string $key, mixed $value)
        {
            $this->staticData = [$key => $value];
        }
    };
}

// Distinct helpers for remove() tests so each has a unique anonymous class name
function makeProviderA(): DataProvider
{
    return new class extends DataProvider {
        public function __construct()
        {
            $this->staticData = ['a' => 'value_a'];
        }
    };
}

function makeProviderB(): DataProvider
{
    return new class extends DataProvider {
        public function __construct()
        {
            $this->staticData = ['b' => 'value_b'];
        }
    };
}
