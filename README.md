# Livewire Searchable Select

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fabioguin/livewire-searchable-select.svg?style=for-the-badge)](https://packagist.org/packages/fabioguin/livewire-searchable-select)
[![Total Downloads](https://img.shields.io/packagist/dt/fabioguin/livewire-searchable-select.svg?style=for-the-badge)](https://packagist.org/packages/fabioguin/livewire-searchable-select)
[![GitHub license](https://img.shields.io/github/license/fabioguin/livewire-searchable-select?style=for-the-badge)](https://github.com/fabioguin/livewire-searchable-select/blob/master/LICENSE)

High-performance Livewire component for searchable select inputs with relevance-based ordering, intelligent caching, and optimized UX.

## Features

- **High Performance**: Optimized database queries with intelligent caching
- **Relevance-Based Ordering**: Smart search results ranked by relevance
- **Debounced Input**: Smooth UX with 300ms debouncing to reduce server load
- **Redis Caching**: Automatic caching of search results for better performance
- **SQL Injection Protection**: Secure input sanitization and validation
- **Responsive Design**: Works perfectly on all device sizes
- **Customizable**: Easy to customize with CSS classes and configuration
- **Model Scopes**: Support for complex model filtering with scopes
- **Multi-language**: Built-in internationalization support

## Requirements
- [Laravel 10 or 11](https://laravel.com/docs/10.x)
- [Livewire 3.0+](https://livewire.laravel.com/)
- [Alpine JS](https://alpinejs.dev/) (included with Livewire)
- [Redis](https://redis.io/) (optional, for caching)

## Installation

You can install the package via composer:

```bash
composer require fabioguin/livewire-searchable-select
```

### Redis Configuration (Optional but Recommended)

For optimal performance, configure Redis as your cache driver:

```bash
# In your .env file
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

The package will automatically use Redis for caching search results, significantly improving performance.

## Basic Usage

- use trait ```SearchableSelect``` in your livewire component:
```php
<?php

namespace App\Http\Livewire\CreateUser;

use Livewire\Component;
use FabioGuin\LivewireSearchableSelect\Traits\SearchableSelect;

class CreateUser extends Component
{
    use SearchableSelect;

    // set properties to get selected value from LivewireSearchableSelect
    public int $country_id;
}

```

- Use the ```livewire-searchable-select``` component in your blade view, and pass in a parameters:
```html

<livewire:select-searchable-input
        property="country_id"
        model-app="\App\Models\Country"
        model-app-scope="isActive"
        option-text="{name}"
        option-value-column="id"
        active-option-text="{{ request()->user()->country_name }}"
        active-option-value="{{ request()->user()->country_id }}"
        :search-columns="['name']"
        :search-min-chars="2"
        :search-limit-results="15"
        input-extra-classes="mt-3"
        input-placeholder="Select country" />

```

## Performance Features

### Intelligent Caching
The component automatically caches search results for 5 minutes, dramatically reducing database load:

```php
// Results are automatically cached with intelligent cache keys
// Cache duration: 5 minutes (configurable)
// Cache driver: Uses your configured cache driver (Redis recommended)
```

### Relevance-Based Ordering
Search results are intelligently ordered by relevance:

- **Exact match**: 100 points
- **Starts with**: 80 points  
- **Contains**: 60 points
- **Ends with**: 40 points

### Debounced Input
Input is debounced by 300ms to prevent excessive server requests:

```html
<!-- Automatically debounced - no configuration needed -->
<livewire:select-searchable-input ... />
```

### Properties
| Property                 | Arguments                                            | Result                                                                                                                                                                                                                          | Example                                                          |
|--------------------------|------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------|
| **property**             | *String - required* property name                    | Define the property name                                                                                                                                                                                                        | ```property="country_id"```                                      |
| **model-app**            | *String - required* full model name                  | Define the source of data that will be select                                                                                                                                                                                   | ```model-app="\App\Models\Country"```                            |
| **model-app-scope**      | *String - optional* name of model sope               | Define model scope for filtering results                                                                                                                                                                                        | ```model-scope-app="isActive"```                                 |
| **option-text**          | *String - required* show column on option            | Define the column(s) in model that want to be show in select option                                                                                                                                                             | ```option-text="{id} - {name} ({abbreviation})"```               |
| **option-value-column**  | *String - required* set value                        | Define the column name as a value data that will be selected                                                                                                                                                                    | ```option-value-column="id"```                                   |
| **active-option-text**   | *Mixed - optional* set active value text             | Define the default selected option to show on select                                                                                                                                                                            | ```active-option-text="{{ request()->user()->country_name }}"``` |
| **active-option-value**  | *Mixed - optional* set active value                  | Define the default selected option value to pass in the model                                                                                                                                                                   | ```active-option-value="{{ request()->user()->country_id }}"```  |
| **search-columns**       | *Array - required* search column                     | Define the column in model that want to be searched                                                                                                                                                                             | ```:search-columns="['name', 'abbreviation']"```                 |
| **search-min-chars**     | *Int - optional* minimum character                   | Define minimum character for trigger search event; default: 0                                                                                                                                                                   | ```:search-min-chars="2"```                                      |
| **search-limit-results** | *Int - optional* max results to view in the dropdown | Define the lenght of result for dropdown; default: 10                                                                                                                                                                           | ```:search-limit-results="15"```                                 |
| **input-extra-classes**  | *String - optional* add extra classes                | Define the extra classes for the input, anyway each element has a class without defined attributes that can be exploited for customization, for example: "select-searchable-input", "select-searchable-input-clear-value", etc. | ```input-extra-classes="mt-3"```                                 |
| **input-placeholder**    | *String - optional* placeholder name                 | Define the placeholder for select input                                                                                                                                                                                         | ```input-placeholder="Select country"```                         |

## model-app-scope
With this parameter you can define a query scope of the model to filter the search results in a complex way (see official Laravel documentation). This makes the component even more flexible and usable in multiple contexts. Remember to use a string with the **camel case** syntax without specifying that it is a "_scope_" (see example).


## Customization
**Livewire Select** is designed to be easily customizable. You can publish and modify the configuration, views, and language files to suit your needs.

### Configuration
You can publish the configuration file with:

```bash
php artisan vendor:publish --provider="FabioGuin\LivewireSearchableSelect\LivewireSearchableSelectServiceProvider" --tag="config"
```

This will publish a **livewire-searchable-select.php** config file to your config directory. Here you can change the default settings of **Livewire Select**.

### Views
If you need to modify the views, you can publish them with:

```bash
php artisan vendor:publish --provider="FabioGuin\LivewireSearchableSelect\LivewireSearchableSelectServiceProvider" --tag="views"
```

This will publish the view files to **resources/views/vendor/livewire-searchable-select**. You can edit these files to change the appearance of the select input.

### Language
To customize the language strings, you can publish the language files with:

```bash
php artisan vendor:publish --provider="FabioGuin\LivewireSearchableSelect\LivewireSearchableSelectServiceProvider" --tag="lang"
```

This will publish the language files to **resources/lang/vendor/livewire-searchable-select**. You can edit these files to change the text used by **Livewire Select**.

## What's New in v2.0.0

### Major Improvements
- **Refactored Architecture**: Complete separation of concerns with Config and Service classes
- **Security Enhanced**: SQL injection protection with input sanitization
- **Performance Optimized**: Intelligent caching and query optimization
- **Smart Ordering**: Relevance-based search result ranking
- **Better UX**: Debounced input for smooth user experience
- **Test Coverage**: Comprehensive test suite for reliability

### Breaking Changes
- **Namespace Update**: `SearchableSelect` trait moved to `FabioGuin\LivewireSearchableSelect\Traits\SearchableSelect`
- **Architecture**: New Config and Service classes for better maintainability
- **Input Handling**: Replaced `wire:model` with debounced Alpine.js input

## Migration from v1.x

### 1. Update Trait Import
```php
// Before (v1.x)
use FabioGuin\LivewireSearchableSelect\SearchableSelect;

// After (v2.0.0)
use FabioGuin\LivewireSearchableSelect\Traits\SearchableSelect;
```

### 2. No Other Changes Required
The component API remains the same, so your existing Blade templates will continue to work without modification.

### 3. Optional: Enable Redis Caching
For better performance, configure Redis as your cache driver (see Installation section above).

## Future Developments

The package is actively maintained with the following planned features:

- **CSS Framework Support**: Tailwind CSS and Bootstrap 4/5 compatibility
- **Advanced Analytics**: Search analytics and performance metrics
- **Real-time Updates**: WebSocket support for live updates
- **Enhanced i18n**: More language support and RTL compatibility
- **Mobile Optimization**: Touch-friendly mobile interactions
- **Advanced Search**: Fuzzy search and typo tolerance

Inspired by https://github.com/mitratek/livewire-select

We ❤️ Semantic Versioning https://semver.org/

Open to work, contact me: https://www.linkedin.com/in/fabio-guin-starzero/