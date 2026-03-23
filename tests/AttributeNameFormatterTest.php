<?php

use Foxbyte\InertiaDataProviders\AttributeNameFormatters\AsWritten;
use Foxbyte\InertiaDataProviders\AttributeNameFormatters\CamelCase;
use Foxbyte\InertiaDataProviders\AttributeNameFormatters\SnakeCase;
use Foxbyte\InertiaDataProviders\DataProvider;

it('AsWritten returns name unchanged', function () {
    $formatter = new AsWritten();

    expect($formatter('some_name'))->toBe('some_name');
    expect($formatter('camelCase'))->toBe('camelCase');
});

it('CamelCase converts snake_case to camelCase', function () {
    $formatter = new CamelCase();

    expect($formatter('snake_case_name'))->toBe('snakeCaseName');
    expect($formatter('already'))->toBe('already');
});

it('SnakeCase converts camelCase to snake_case', function () {
    $formatter = new SnakeCase();

    expect($formatter('camelCaseName'))->toBe('camel_case_name');
    expect($formatter('already'))->toBe('already');
});

it('formats DataProvider output keys with CamelCase formatter', function () {
    config()->set('inertia-dataproviders.attribute_name_formatter', CamelCase::class);

    $provider = new class extends DataProvider {
        public function __construct()
        {
            $this->staticData = ['snake_key' => 'value'];
        }
    };

    expect($provider->toArray())->toHaveKey('snakeKey', 'value');
});

it('formats DataProvider output keys with SnakeCase formatter', function () {
    config()->set('inertia-dataproviders.attribute_name_formatter', SnakeCase::class);

    $provider = new class extends DataProvider {
        public string $camelKey = 'value';
    };

    expect($provider->toArray())->toHaveKey('camel_key', 'value');
});

it('formats DataProvider output keys with AsWritten formatter', function () {
    config()->set('inertia-dataproviders.attribute_name_formatter', AsWritten::class);

    $provider = new class extends DataProvider {
        public string $originalName = 'value';
    };

    expect($provider->toArray())->toHaveKey('originalName', 'value');
});
