# Livewire Select

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fabioguin/livewire-searchable-select.svg?style=for-the-badge)](https://packagist.org/packages/fabioguin/livewire-searchable-select)
[![Total Downloads](https://img.shields.io/packagist/dt/fabioguin/livewire-searchable-select.svg?style=for-the-badge)](https://packagist.org/packages/fabioguin/livewire-searchable-select)
[![GitHub license](https://img.shields.io/github/license/fabioguin/livewire-searchable-select?style=for-the-badge)](https://github.com/fabioguin/livewire-searchable-select/blob/master/LICENSE)

Livewire component for searchable select inputs

## Requirements
- [Laravel 10 or 11](https://laravel.com/docs/10.x)
- [Livewire](https://livewire.laravel.com/)
- [Tailwind](https://tailwindcss.com/)
- [Alpine JS](https://alpinejs.dev/)

## Installation

You can install the package via composer:

```bash
composer require fabioguin/livewire-searchable-select
```

## Basic Usage

- use trait ```SearchableSelect``` in your livewire component:
```php
<?php

namespace App\Http\Livewire\CreateUser

use Livewire\Component;
use FabioGuin\LivewireSearchableSelect\SearchableSelect;

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
        :search-columns="['name']"
        option-text="{name}"
        option-value="id"
        active-option-text="{{ request()->user()->country_name }}"
        active-option-value="{{ request()->user()->country_id }}"
        :min-chars-to-search="2"
        :max-result-list-length="15"
        input-extra-classes="mt-3"
        input-placeholder="Select country" />

```

### Properties
| Property                   | Arguments                                            | Result                                                                                                                                                                                                                          | Example                                                          |
|----------------------------|------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------|
| **property**               | *String - required* property name                    | Define the property name                                                                                                                                                                                                        | ```property="country_id"```                                      |
| **model-app**              | *String - required* full model name full collection  | Define the source of data that will be select                                                                                                                                                                                   | ```model-app="\App\Models\Country"```                            |
| **search-columns**         | *Array - required* search column                     | Define the column in model that want to be searched                                                                                                                                                                             | ```:search-columns="['name', 'abbreviation']"```                 |
| **option-text**            | *String - required* show column on option            | Define the column(s) in model that want to be show in select option                                                                                                                                                             | ```option-text="{id} - {name} ({abbreviation})"```               |
| **option-value**           | *String - required* set value                        | Define the column name as a value data that will be selected                                                                                                                                                                    | ```option-value="id"```                                          |
| **active-option-text**     | *Mixed - optional* set active value text             | Define the default selected option to show on select                                                                                                                                                                            | ```active-option-text="{{ request()->user()->country_name }}"``` |
| **active-option-value**    | *Mixed - optional* set active value                  | Define the default selected option value to pass in the model                                                                                                                                                                   | ```active-option-value="{{ request()->user()->country_id }}"```  |
| **min-chars-to-search**    | *Int - optional* minimum character                   | Define minimum character for trigger search event; default: 0                                                                                                                                                                   | ```:min-chars-to-search="2"```                                   |
| **max-result-list-length** | *Int - optional* max results to view in the dropdown | Define the lenght of result for dropdown; default: 10                                                                                                                                                                           | ```:max-result-list-length="15"```                               |
| **input-extra-classes**    | *String - optional* add extra classes                | Define the extra classes for the input, anyway each element has a class without defined attributes that can be exploited for customization, for example: "select-searchable-input", "select-searchable-input-clear-value", etc. | ```input-extra-classes="mt-3"```                                 |
| **input-placeholder**      | *String - optional* placeholder name                 | Define the placeholder for select input                                                                                                                                                                                         | ```input-placeholder="Select country"```                         |

## Customization
**Livewire Select** is designed to be easily customizable. You can publish and modify the configuration, views, and language files to suit your needs.

### Configuration
You can publish the configuration file with:

```bash
php artisan vendor:publish --provider="FabioGuin\LivewireSearchableSelect\LivewireSelectServiceProvider" --tag="config"
```

This will publish a **livewire-searchable-select.php** config file to your config directory. Here you can change the default settings of **Livewire Select**.

### Views
If you need to modify the views, you can publish them with:

```bash
php artisan vendor:publish --provider="FabioGuin\LivewireSearchableSelect\LivewireSelectServiceProvider" --tag="views"
```

This will publish the view files to **resources/views/vendor/livewire-searchable-select**. You can edit these files to change the appearance of the select input.

### Language
To customize the language strings, you can publish the language files with:

```bash
php artisan vendor:publish --provider="FabioGuin\LivewireSearchableSelect\LivewireSelectServiceProvider" --tag="lang"
```

This will publish the language files to **resources/lang/vendor/livewire-searchable-select**. You can edit these files to change the text used by **Livewire Select**.

## TODO and Future Developments
The **Livewire Select** package is currently under development and there are several features and improvements planned for future releases. 

Here is a list of the planned tasks:

- **Support for Tailwind CSS and Bootstrap 4:** Currently, the views are designed for Bootstrap 5. However, the goal is to add support for Tailwind CSS and Bootstrap 4. This will require creating separate versions of the views for each of these CSS frameworks.
- **Documentation Improvements:** The documentation will be continuously updated to reflect new features and improvements. This includes adding more examples and tutorials.
- **Testing and Bug Fixes:** We will continue to test the package to identify and fix any bugs. If you find a bug, please report it via the “Issues” section on GitHub.
- **Performance Improvements:** We are always looking for ways to improve the performance of the package. This could include optimizing the code and adding new features to improve efficiency.
- **Support for Other Livewire Features:** We are exploring the possibility of adding support for other Livewire features, such as pagination and lazy loading.

Inspired by https://github.com/mitratek/livewire-select