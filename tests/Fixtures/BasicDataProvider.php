<?php

namespace Foxbyte\InertiaDataProviders\Tests\Fixtures;

use Closure;
use Foxbyte\InertiaDataProviders\DataProvider;
use Illuminate\Contracts\Support\Arrayable;
use Inertia\AlwaysProp;
use Inertia\DeferProp;
use Inertia\LazyProp;
use Inertia\MergeProp;
use Inertia\OnceProp;
use Inertia\OptionalProp;
use Inertia\ScrollProp;

class BasicDataProvider extends DataProvider
{
    public static string $staticProp = 'should be excluded';

    public string $title = 'Hello World';

    public Arrayable $arrayable;

    public function __construct()
    {
        $this->staticData = [
            'static_key' => 'static_value',
        ];

        $this->arrayable = new class implements Arrayable {
            public function toArray(): array
            {
                return ['nested' => 'value'];
            }
        };
    }

    public function plainMethod(): string
    {
        return 'plain value';
    }

    public function alwaysProp(): AlwaysProp
    {
        return new AlwaysProp(fn () => 'always value');
    }

    public function deferProp(): DeferProp
    {
        return new DeferProp(fn () => 'defer value');
    }

    public function lazyProp(): LazyProp
    {
        return new LazyProp(fn () => 'lazy value');
    }

    public function mergeProp(): MergeProp
    {
        return new MergeProp(fn () => ['merge' => 'value']);
    }

    public function onceProp(): OnceProp
    {
        return new OnceProp(fn () => 'once value');
    }

    public function optionalProp(): OptionalProp
    {
        return new OptionalProp(fn () => 'optional value');
    }

    public function scrollProp(): ScrollProp
    {
        return new ScrollProp(fn () => 'scroll value');
    }

    public function closureProp(): Closure
    {
        return fn () => 'closure value';
    }
}
