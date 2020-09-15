<?php


namespace Guerrilla\RequestFilters;

use Guerrilla\RequestFilters\Filters\FilterCapitalize;
use Guerrilla\RequestFilters\Filters\FilterDate;
use Guerrilla\RequestFilters\Filters\FilterNumeric;
use Guerrilla\RequestFilters\Filters\FilterStripTags;
use Guerrilla\RequestFilters\Filters\FilterToLower;
use Guerrilla\RequestFilters\Filters\FilterToUpper;
use Guerrilla\RequestFilters\Filters\FilterTrim;
use Guerrilla\RequestFilters\Filters\Sanitization\FilterSanitizeEmail;
use Guerrilla\RequestFilters\Filters\Sanitization\FilterSanitizeEncoded;
use Guerrilla\RequestFilters\Filters\Sanitization\FilterSantizeText;

/**
 * A tool to parse various inputs into a format that can be used when using the InputFilter.
 *
 * @package Guerrilla\RequestFilters
 */
class FilterParser
{

    /**
     * An associated array of supported filters
     * @return array
     */
    public static function getSupportedFilters() : array{
        return [

            'lowercase' => new FilterToLower(),
            'uppercase' => new FilterToUpper(),
            'trim' => new FilterTrim(),
            'strip' => new FilterStripTags(),
            'date' => new FilterDate(''),
            'capitalize' => new FilterCapitalize(),
            'number' => new FilterNumeric(),
            'sanitize' => new FilterSantizeText(),
            'email' => new FilterSanitizeEmail(),
            'encode' => new FilterSanitizeEncoded()
        ];
    }

    /**
     * Parse a string into a collection of filter rules.
     * @param string $filter_string e.g 'uppercase|trim|date:d/m/Y'
     * @param array|null $external_filters e.g 'custom1|custom2|somethingwithparams:hello world'
     * @return array An array of filter data.
     * [
     *  [
     *      'filter' => FilterInstance(),
     *      'params' => [1,2,3...]
     *  ]
     * ]
     */
    public static function parseFilterString(string $filter_string, array $external_filters = null) : array{

        $filters = self::getSupportedFilters();

        if(!empty($external_filters)){
            $filters = array_merge($filters, $external_filters);
        }

        $tokens = explode('|', $filter_string);

        $instances = [];

        foreach($tokens as $token){

            $key = $token;

            $params = explode(':', $token);
            $has_params = count($params) > 1;

            if($has_params){
                $key = $params[0];
            }

            if(array_key_exists($key, $filters)){

                if($has_params){
                    array_push($instances, [
                        'filter'=>$filters[$key],
                        'params' => array_slice($params, 1)
                    ]);
                }else{

                    array_push($instances, [
                        'filter'=>$filters[$key],
                        'params' => null
                    ]);
                }
            }
        }

        return $instances;
    }





}