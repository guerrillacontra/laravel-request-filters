<?php


namespace Guerrilla\RequestFilters\Filters;


/**
 * A request filter that will filter an input to upper case
 * @package Guerrilla\RequestFilters\Filters
 */
class FilterToUpper implements FilterInterface
{

    function applyFilter(string $input): string
    {
        return mb_strtoupper($input);
    }
}
