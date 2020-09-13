<?php


namespace Guerrilla\RequestFilters\Filters;


/**
 * A request filter that will filter out leading and trailing white space
 * @package Guerrilla\RequestFilters\Filters
 */
class FilterTrim implements FilterInterface
{

    function applyFilter(string $input): string
    {
        return trim($input);
    }
}
