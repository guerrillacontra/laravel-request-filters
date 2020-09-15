<?php


namespace Guerrilla\RequestFilters\Filters;


/**
 * A request filter that will filter out all non-numeric characters.
 * @package Guerrilla\RequestFilters\Filters
 */
class FilterNumeric implements FilterInterface
{
    function applyFilter(string $input, array $params = null): string
    {
        return preg_replace('/[^0-9]/si', '', $input);
    }
}
