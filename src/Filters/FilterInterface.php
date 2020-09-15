<?php


namespace Guerrilla\RequestFilters\Filters;


/**
 * An interface that can be used to implement a Laravel request filter
 * @package Guerrilla\RequestFilters\Filters
 */
interface FilterInterface
{
    /**
     * Apply the filter to an input string and return a filtered input string
     * @param string $input the input value that should not be null
     * @param array|null $params Optional params that can be provided at runtime to override configuration
     * @return string a filtered input value
     */
    function applyFilter(string $input, array $params = null) : string;
}
