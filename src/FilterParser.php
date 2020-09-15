<?php


namespace Guerrilla\RequestFilters;

use Guerrilla\RequestFilters\Filters\FilterCapitalize;
use Guerrilla\RequestFilters\Filters\FilterDate;
use Guerrilla\RequestFilters\Filters\FilterInterface;
use Guerrilla\RequestFilters\Filters\FilterNumeric;
use Guerrilla\RequestFilters\Filters\FilterStripTags;
use Guerrilla\RequestFilters\Filters\FilterToLower;
use Guerrilla\RequestFilters\Filters\FilterToUpper;
use Guerrilla\RequestFilters\Filters\FilterTrim;
use Guerrilla\RequestFilters\Filters\Sanitization\FilterSanitizeEmail;
use Guerrilla\RequestFilters\Filters\Sanitization\FilterSanitizeEncoded;
use Guerrilla\RequestFilters\Filters\Sanitization\FilterSantizeText;
use ReflectionClass;
use ReflectionMethod;

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

            'lowercase' => FilterToLower::class,
            'uppercase' =>  FilterToUpper::class,
            'trim' =>  FilterTrim::class,
            'strip' =>  FilterStripTags::class,
            'date' =>  FilterDate::class,
            'capitalize' =>  FilterCapitalize::class,
            'number' =>  FilterNumeric::class,
            'sanitize' =>  FilterSantizeText::class,
            'email' =>  FilterSanitizeEmail::class,
            'encode' =>  FilterSanitizeEncoded::class
        ];
    }

    /**
     * Parse a string into a collection of filter rules.
     * @param string $filter_string e.g 'uppercase|trim|date:d/m/Y'
     * @param array|null $external_filters e.g 'custom1|custom2|somethingwithparams:hello world'
     * @return array An array of filters.
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

                    array_push($instances, self::createReflectedFilter($filters[$key],array_slice($params, 1)) );

                }else{

                    array_push($instances, self::createReflectedFilter($filters[$key],[]) );
                }
            }
        }

        return $instances;
    }



    private static function createReflectedFilter($class_name, $args): FilterInterface{


        $refClass = new ReflectionClass($class_name);

        if(!$refClass->hasMethod('__construct')){

            /** @var FilterInterface $instance */
            $instance = $refClass->newInstance();

            return $instance;
        }

        $refMethod = new ReflectionMethod($class_name,  '__construct');
        $params = $refMethod->getParameters();

        $re_args = array();

        foreach($params as $key => $param)
        {
            if ($param->isPassedByReference())
            {
                $re_args[$key] = &$args[$key];
            }
            else
            {
                $re_args[$key] = $args[$key];
            }
        }

        /** @var FilterInterface $class_instance */
        $class_instance = $refClass->newInstanceArgs((array) $re_args);
        return $class_instance;
    }




}