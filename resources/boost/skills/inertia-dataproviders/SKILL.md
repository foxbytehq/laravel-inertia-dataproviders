---
name: laravel:inertia-dataproviders
description: |
  Inertia Data Providers for Laravel - encapsulate data composition logic for Inertia.js views, keeping controllers clean and slim. Use this skill when: (1) creating data providers for Inertia pages, (2) working with Inertia::render() and page data, (3) implementing lazy, deferred, or optional props, (4) combining multiple data sources for a single Inertia view, (5) refactoring fat controllers that pass data to Inertia views.
---

# Inertia Data Providers

Data providers extract data composition for Inertia views into dedicated classes, keeping controllers slim.

## Creating a Data Provider

```bash
php artisan make:data-provider UserProfileDataProvider
```

Creates `app/Http/DataProviders/UserProfileDataProvider.php`.

## Basic Structure

```php
<?php

namespace App\Http\DataProviders;

use Foxbyte\InertiaDataProviders\DataProvider;

class ExampleDataProvider extends DataProvider
{
    // Public properties become page data
    public string $title = 'Page Title';

    // Public methods become page data (with dependency injection)
    public function users(UserRepository $repo): array
    {
        return $repo->getActive();
    }

    // Use staticData for simple values
    public function __construct(public Model $model)
    {
        $this->staticData = [
            'id' => $model->id,
        ];
    }

    // Protected/private methods are NOT exposed
    protected function helper(): string
    {
        return 'internal';
    }
}
```

## Using in Controllers

```php
use Inertia\Inertia;
use App\Http\DataProviders\PostDataProvider;

// As sole data source
return Inertia::render('Posts/Show', new PostDataProvider($post));

// Mixed with other data
return Inertia::render('Posts/Show', [
    'meta' => $meta,
    'post' => new PostDataProvider($post),
]);
```

## Inertia Prop Types

```php
use Inertia\Inertia;
use Closure;

class PostDataProvider extends DataProvider
{
    // DeferProp - loaded after initial page render
    public function comments(): DeferProp
    {
        return Inertia::defer(fn () => $this->post->comments);
    }

    // LazyProp - only loaded on partial reloads when requested
    public function analytics(): LazyProp
    {
        return Inertia::lazy(fn () => $this->post->getAnalytics());
    }

    // Closure - shorthand lazy prop
    public function relatedPosts(): Closure
    {
        return fn () => Post::related($this->post)->get();
    }

    // AlwaysProp - always included (even in partial reloads)
    public function flash(): AlwaysProp
    {
        return Inertia::always(fn () => session('flash'));
    }

    // OnceProp - only on first visit, not partial reloads
    public function initialConfig(): OnceProp
    {
        return Inertia::once(fn () => config('app.frontend'));
    }
}
```

## Multiple Data Providers

```php
use Foxbyte\InertiaDataProviders\DataProvider;

// Merge multiple providers
return Inertia::render('Dashboard', DataProvider::collection(
    new NavigationDataProvider(),
    new StatsDataProvider($user),
    new NotificationsDataProvider($user),
));

// Conditional providers
return Inertia::render('Dashboard', DataProvider::collection(
    new BaseDataProvider(),
)->when($user->isAdmin(), fn ($c) => $c->add(new AdminDataProvider()))
  ->unless($user->isGuest(), fn ($c) => $c->add(new UserDataProvider($user))));
```

## JSON Responses

```php
// Return as nested array for API responses
return (new PostDataProvider($post))->toNestedArray();
```

## Attribute Formatting

Configure in `config/inertia-dataproviders.php`:

```php
'attribute_name_formatter' => \Foxbyte\InertiaDataProviders\AttributeNameFormatters\CamelCase::class,
```

Options: `AsWritten` (default), `SnakeCase`, `CamelCase`.
