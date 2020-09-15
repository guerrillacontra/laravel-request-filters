<?php


namespace Guerrilla\RequestFilters\Filters\Sanitization;


use Guerrilla\RequestFilters\Filters\FilterInterface;

/**
 * A request filter than can be used to sanitize email input,
 * removing all illegal characters from an email address.
 * @package Guerrilla\RequestFilters\Filters
 */
class FilterSanitizeEmail implements FilterInterface
{
    /**
     * @inheritDoc
     */
    function applyFilter(string $input): string
    {
        return filter_var($input, FILTER_SANITIZE_EMAIL);
    }
}