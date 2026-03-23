<?php

use Foxbyte\InertiaDataProviders\DataProvider;
use Foxbyte\InertiaDataProviders\DataProviderCollection;
use Foxbyte\InertiaDataProviders\Tests\Fixtures\BasicDataProvider;
use Illuminate\Contracts\Support\Arrayable;
use Inertia\AlwaysProp;
use Inertia\DeferProp;
use Inertia\LazyProp;
use Inertia\MergeProp;
use Inertia\OnceProp;
use Inertia\OptionalProp;
use Inertia\ScrollProp;

beforeEach(function () {
    $this->provider = new BasicDataProvider();
    $this->array = $this->provider->toArray();
});

it('includes public properties in toArray output', function () {
    expect($this->array)->toHaveKey('title', 'Hello World');
});

it('includes static data in toArray output', function () {
    expect($this->array)->toHaveKey('static_key', 'static_value');
});

it('includes public method return values wrapped in closures for DI', function () {
    expect($this->array)->toHaveKey('plainMethod');
    expect($this->array['plainMethod'])->toBeInstanceOf(Closure::class);
    expect(app()->call($this->array['plainMethod']))->toBe('plain value');
});

it('excludes static properties', function () {
    expect($this->array)->not->toHaveKey('staticProp');
});

it('excludes excluded methods', function () {
    expect($this->array)
        ->not->toHaveKey('toArray')
        ->not->toHaveKey('toJson')
        ->not->toHaveKey('toNestedArray')
        ->not->toHaveKey('dd')
        ->not->toHaveKey('dump');
});

it('resolves Arrayable properties to arrays', function () {
    expect($this->array)->toHaveKey('arrayable');
    expect($this->array['arrayable'])->toBe(['nested' => 'value']);
});

it('returns AlwaysProp directly without wrapping', function () {
    expect($this->array['alwaysProp'])->toBeInstanceOf(AlwaysProp::class);
});

it('returns DeferProp directly without wrapping', function () {
    expect($this->array['deferProp'])->toBeInstanceOf(DeferProp::class);
});

it('returns LazyProp directly without wrapping', function () {
    expect($this->array['lazyProp'])->toBeInstanceOf(LazyProp::class);
});

it('returns MergeProp directly without wrapping', function () {
    expect($this->array['mergeProp'])->toBeInstanceOf(MergeProp::class);
});

it('returns OnceProp directly without wrapping', function () {
    expect($this->array['onceProp'])->toBeInstanceOf(OnceProp::class);
});

it('returns OptionalProp directly without wrapping', function () {
    expect($this->array['optionalProp'])->toBeInstanceOf(OptionalProp::class);
});

it('returns ScrollProp directly without wrapping', function () {
    expect($this->array['scrollProp'])->toBeInstanceOf(ScrollProp::class);
});

it('returns Closure directly without wrapping', function () {
    expect($this->array['closureProp'])->toBeInstanceOf(Closure::class);
    expect(($this->array['closureProp'])())->toBe('closure value');
});

it('merges with correct precedence: methods override properties override static data', function () {
    $provider = new class extends DataProvider {
        public string $key = 'from property';

        public function __construct()
        {
            $this->staticData = ['key' => 'from static'];
        }

        public function key(): string
        {
            return 'from method';
        }
    };

    $array = $provider->toArray();

    // Method wraps in closure for DI, so the key should be a Closure
    expect($array['key'])->toBeInstanceOf(Closure::class);
    expect(app()->call($array['key']))->toBe('from method');
});

it('returns a DataProviderCollection from collection() static method', function () {
    $collection = BasicDataProvider::collection(new BasicDataProvider());
    expect($collection)->toBeInstanceOf(DataProviderCollection::class);
});

it('resolves lazy props in toNestedArray', function () {
    $provider = new class extends DataProvider {
        public function lazyValue(): LazyProp
        {
            return new LazyProp(fn () => 'resolved lazy');
        }

        public function simpleValue(): string
        {
            return 'simple';
        }
    };

    $nested = $provider->toNestedArray();

    // LazyProp is resolved (called) by resolvePropertyInstances
    expect($nested['lazyValue'])->toBe('resolved lazy');
    // Regular method gets resolved through closure
    expect($nested['simpleValue'])->toBe('simple');
});

it('returns valid JSON from toJson', function () {
    $provider = new class extends DataProvider {
        public string $name = 'test';
    };

    $json = $provider->toJson();

    expect($json)->toBeString();
    expect(json_decode($json, true))->toBe(['name' => 'test']);
});
