<?php


namespace Guerrilla\RequestFilters\Filters;


/**
 * A request filter that will strip out HTML and PHP tags
 * from the input text.
 *
 * You may include tags that you wish to preserve.
 *
 * Example:
 * new FilterStripTags('<a><p>')
 *
 * @package Guerrilla\RequestFilters\Filters
 */
class FilterStripTags implements FilterInterface
{
    public $allowable_tags;

    function __construct(string $allowable_tags = null){
        $this->allowable_tags = $allowable_tags;
    }

    function applyFilter(string $input): string
    {
        return strip_tags($input, $this->allowable_tags);
    }
}
