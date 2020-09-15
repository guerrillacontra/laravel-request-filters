<?php


namespace Guerrilla\RequestFilters;

/**
 * Class RequestFiltering
 * @package Guerrilla\RequestFilters
 */
class InputFilter
{

    /**
     * Filter an associative array of inputs with an associative array of matching filters
     * @param array $inputs an associative array of request inputs key => value
     *
     * [
     *  'key' => 'value'
     * ]
     *
     * @param array $filters an associative array of request filters key => FilterInterface[]
     *
     * [
     *  'key' => [new FilterTrim, new FilterToUpper],
     *  'key.nestedkey' => [...],
     *  'key.*.arraykey' => [...]
     * ]
     *
     * @throws \Exception when the rules are not defined correctly
     *
     * @return array an associative array of filtered inputs
     *
     * [
     *  'key' => 'value'
     * ]
     */
    public static function filter(array $inputs, array $filters) : array{

        foreach($filters as $filter_input_key => $filter_array) {

            $tokens = explode('.', $filter_input_key);

            $token_count = count($tokens);

            if ($token_count === 0) continue;

            $parent = &$inputs;

            for ($token_index = 0; $token_index < $token_count; $token_index++) {

                $current_token = $tokens[$token_index];

                if ($current_token === '*') {

                    $next_token = $tokens[$token_index + 1];

                    for ($parent_array_index = 0; $parent_array_index < count($parent); $parent_array_index++) {

                        $container = &$parent[$parent_array_index];

                        foreach ($filter_array as $filter) {

                            if (!empty($container[$next_token])) {
                                $container[$next_token] = $filter->applyFilter($container[$next_token]);
                            }
                        }
                    }

                    break;
                }

                if ($token_index !== $token_count - 1) {
                    $parent = &$parent[$current_token];

                } else if ($token_index === $token_count - 1) {

                    foreach ($filter_array as $filter) {

                        if (!empty($parent[$current_token])) {
                            $parent[$current_token] = $filter->applyFilter($parent[$current_token]);
                        }
                    }
                }
            }
        }

        return $inputs;
    }

    /**
     * Filter on a string representation of an array of filters.
     * @param array $inputs an associative array of request inputs key => value
     *
     * [
     *  'key' => 'value'
     * ]
     * @param array $filters_as_text An associative array of filters where each filter value is a string representing
     * the filters you want similar to Laravels validation.
     *
     * [
     *  'key' => 'trim|uppercase|date:d m Y|strip'
     * ]
     * @param array|null $custom_filters An associated array of custom filters that can be dynamically instantiated along with the native
     * filters found in FilterParser::getSupportedFilters()
     *
     * [
     *  'yourcustomfilter' => CustomFilter::class
     * ]
     * @return array an associadtive array of filtered inputs
     *
     * [
     *  'key' => 'value'
     * ]
     * @throws \Exception For Invalid filter text or custom filter data
     */
    public static function filterFromString(array $inputs, array $filters_as_text, array $custom_filters = null) : array{

        $filters = [];

        foreach($filters_as_text as $key => $text_array){

            $filters[$key] = FilterParser::parseFilterString($text_array, $custom_filters) ;

        }

        return self::filter($inputs, $filters);
    }

}