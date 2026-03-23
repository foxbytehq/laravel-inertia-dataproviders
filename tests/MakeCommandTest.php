<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->generatedPath = app_path('Http/DataProviders/TestProvider.php');
});

afterEach(function () {
    if (File::exists($this->generatedPath)) {
        File::delete($this->generatedPath);
    }

    // Clean up parent directory if empty
    $dir = dirname($this->generatedPath);
    if (File::isDirectory($dir) && empty(File::files($dir))) {
        File::deleteDirectory($dir);
    }
});

it('creates a data provider file', function () {
    $this->artisan('make:data-provider', ['name' => 'TestProvider'])
        ->assertSuccessful();

    expect(File::exists($this->generatedPath))->toBeTrue();
});

it('generates a class extending DataProvider', function () {
    $this->artisan('make:data-provider', ['name' => 'TestProvider'])
        ->assertSuccessful();

    $content = File::get($this->generatedPath);

    expect($content)
        ->toContain('extends DataProvider')
        ->toContain('use Foxbyte\InertiaDataProviders\DataProvider;')
        ->toContain('namespace App\Http\DataProviders;');
});

it('overwrites existing file with --force flag', function () {
    $this->artisan('make:data-provider', ['name' => 'TestProvider'])
        ->assertSuccessful();

    // Second call without --force should fail
    $this->artisan('make:data-provider', ['name' => 'TestProvider'])
        ->assertFailed();

    // With --force should succeed
    $this->artisan('make:data-provider', ['name' => 'TestProvider', '--force' => true])
        ->assertSuccessful();
});
