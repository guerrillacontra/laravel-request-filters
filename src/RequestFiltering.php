<?php


namespace Guerrilla\RequestFilters;

class RequestFiltering
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
     *  'key' => [new FilterTrim, new FilterToUpper]
     * ]
     *
     * @return array an associative array of filtered inputs
     *
     * [
     *  'key' => 'value'
     * ]
     */
    public static function filter(array $inputs, array $filters) : array{

        foreach($filters as $input_key => $filter_array){

            if(empty($inputs[$input_key])){
                continue;
            }

            foreach($filter_array as $filter){

                $inputValue = $inputs[$input_key];

                if(empty($inputValue)){
                    continue;
                }

                $inputs[$input_key] = $filter->applyFilter( $inputValue);
            }
        }

        return $inputs;
    }


}