
# Laravel request filters
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/guerrilla/laravel-request-filters.svg?style=flat-square)](https://packagist.org/packages/guerrilla/laravel-request-filters)
[![Total Downloads](https://img.shields.io/packagist/dt/guerrilla/laravel-request-filters.svg?style=flat-square)](https://packagist.org/packages/guerrilla/laravel-request-filters)

## About

Laravel provides tools to validate HTTP requests allowing developers to ensure the input data
is in the correct structure.

This package provides tools to filter the valid data into the format intended.

It feels like it is part of the Laravel framework and couldn't be any simpler to use.

## Requirements

Laravel 5.6+

## Features

- Format input with a collection of pre-made and tested Filters
- ```FilterRequests``` trait that easily plugs into a ```FormRequest``` and enable filtering
- ```InputFilter``` that allows developers to easily implement their own filters
- ```RequestFiltering``` tool that can apply the same filters to any string you pass in
- [Nested AND array filtering just like Laravel's own validator :ok_hand:](https://laravel.com/docs/7.x/validation#validating-arrays)
- String based filtering similar to Laravel's validator + custom parsable filters

## Included Filters

| Filter class | Usage |
| -------------| ------------- |
| FilterCapitalize | Capitalizes the first character of each word |
| FilterSanitize | Escapes characters based on php's own validator constants |
| FilterSanitizeEmail | Sanitizes email |
| FilterSanitizeText | Sanitizes text generically |
| FilterSanitizeEncoded | URL Encodes text |
| FilterNumeric | Removes all non-numerical characters |
| FilterStripTag | Removes HTML and PHP tags, keeps what you want |
| FilterToLower | Converts to lowercase |
| FilterToUpper | Converts to uppercase |
| FilterTrim | Trim leading and trailing white space |
| FilterDate | Format into a specified Carbon date string see [Carbon docs](https://carbon.nesbot.com/docs/#api-formatting)  |

## Included Filters (as string literals)

| Filter class | Usage |
| -------------| ------------- |
| 'capitalize' | Capitalizes the first character of each word |
| 'email' | Sanitizes email |
| 'sanitize' | Sanitizes text generically |
| 'encode' | URL Encodes text |
| 'number' | Removes all non-numerical characters |
| 'strip' | Removes HTML and PHP tags, keeps what you want |
| 'lowercase' | Converts to lowercase |
| 'uppercase' | Converts to uppercase |
| 'trim' | Trim leading and trailing white space |
| 'date' | Format into a specified Carbon date string see [Carbon docs](https://carbon.nesbot.com/docs/#api-formatting)  |

You can make your own custom filters by implementing the ```FilterInterface``` :)

## How to use

Import via composer:

```composer require guerrilla/laravel-request-filters```

In your [FormRequest](https://laravel.com/docs/7.x/validation#form-request-validation) use the following trait:

```use Guerrilla\RequestFilters\FilterRequests```

Describe your filters (Laravel rules included for familiarisation):

```php
public function rules():array {
    return [
        'email' => ['required', 'email', 'bail'],
        'name' => ['required'],
        'employees.*.name' =>['required']
    ];
}
```

```php
public function filters():array {
    return [
        'email' => [new FilterTrim, new FilterSanitizeEmail],
        'name' => [new FilterTrim, new FilterSanitizeText, new FilterCapitalize],
        'employees.*.name' => [new FilterCapitalize],
        'date' => [new FilterTrim, new FilterDate('d/m/Y')]
    ];
}
```

Or use the string based syntax:
```php
public function filters():array {
    return [
        'email' => 'trim|email',
        'name' => 'trim|sanitize|capitalize',
        'employees.*.name' => 'capitalize',
        'date' => 'trim|date:d/m/Y'
    ];
}
```

Validate the request as per normal but, the results will be now filtered :)

```php
$input = $request->validated();
echo $input['email'];
//trimmed and sanitized email!
```


You can optionally just run the filter on any string you like outside of the request:

```php
$validated_input = $request->validate([...]);

$filtered_result = InputFilter::filter(
                  $validated_input,
                  [
                  'email' => [new FilterTrim],
                  'name' => [new FilterTrim],
                  'meta.*.attributes' => [new MyCustom1Filter(1), new MyCustom2Filter(2)] 
                  ]
);
```

Using your own custom filtering rules to the string parsing syntax is easy!
```php
$validated_input = $request->validate([...]);

$custom_filters = [
    'custom1' => MyCustom1Filter::class,
    'custom2' => MyCustom2Filter::class
];

$filtered_result = InputFilter::filterFromString(
                  $validated_input,
                  [
                  'email' => 'trim',
                  'name' => 'trim',
                  'meta.*.attributes' => 'custom1:1|custom2:2'
                  ],
                  $custom_filters
);
```

## License

[MIT](http://opensource.org/licenses/MIT)
