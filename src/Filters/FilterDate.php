<?php


namespace Guerrilla\RequestFilters\Filters;


use Carbon\Carbon;

/**
 * A request filter that can format an input date into a Carbon date string
 * using formatting found https://carbon.nesbot.com/docs/#api-formatting.
 *
 * @package Guerrilla\RequestFilters\Filters
 */
class FilterDate implements FilterInterface
{
    public $format;

    /**
     * FilterDate constructor.
     * @param string $format A Carbon format string (see https://carbon.nesbot.com/docs/#api-formatting)
     * For example:
     * 07/11/1990 = d/m/Y
     * 7th November 1990 = jS M Y
     */
    function __construct(string $format = null)
    {
        $this->format = $format;
    }

    /**
     * @inheritDoc
     */
    function applyFilter(string $input, array $params = null): string
    {
        $format = $this->format;

        if(!empty($params)){
            $format = $params[0];
        }

        if (empty($format)) return $input;

        $c_date = Carbon::createFromFormat($this->format, $input);

        return $c_date->toDateString();
    }
}