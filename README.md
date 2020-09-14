# Laravel request filters

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
- ```FilterInterface``` that allows developers to easily implement their own filters
- ```RequestFiltering``` tool that can apply the same filters to any string you pass in
- Type safety, no string literals to remember, just standard classes and no services

## Included Filters

| Filter class | Usage |
| -------------| ------------- |
| FilterCapitalize | Capitalizes the first character of each word |
| FilterEscape | Escapes characters based on php's own validator constants |
| FilterNumeric | Removes all non-numerical characters |
| FilterStripTag | Removes HTML and PHP tags, keeps what you want |
| FilterToLower | Converts to lowercase |
| FilterToUpper | Converts to uppercase |
| FilterTrim | Trim leading and trailing white space |
| FilterDate | Format into a specified Carbon date string see [Carbon docs](https://carbon.nesbot.com/docs/#api-formatting)  |

## How to use

Import via composer:

```composer require guerrilla/laravel-request-filters```

In your FormRequest use the following trait:

```use Guerrilla\RequestFilters\FilterRequests```

Describe your filters:

```php
//rules included for familiarisation

public function rules():array {
    return [
        'email' => ['required', 'email', 'bail'],
        'name' => ['required']
    ];
}

public function filters():array {
    return[
        'email' => [new FilterTrim, new FilterEscape(FILTER_SANITIZE_EMAIL)],
        'name' => [new FilterTrim, new FilterEscape(FILTER_SANITIZE_STRING), new FilterCapitalize]
    ];
}
```



Validate the request as per normal but, the results will be filtered :)

```php
$input = $request->validated();
echo $input['email'];
//trimmed and sanitized email!
```


You can optionally just run the filter on any string you like outside of the request:

```php
$filtered_result = RequestFiltering::filter('email', [new FilterTrim, new FilterEscape(FILTER_SANITIZE_EMAIL)]);
```

## Future

Any ideas or changes are welcome, I plan to add text based filtering rules similar to laravels
standard associated validators (e.g ```'trim|escape|tolower'```) as well as plenty of other Filters.

If there is a filter you would like to see please let me know.


## License

[MIT](http://opensource.org/licenses/MIT)
