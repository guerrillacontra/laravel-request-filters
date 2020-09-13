<?php


namespace Guerrilla\RequestFilters\Filters;


/**
 * A request filter that can escape text in various ways using the native php text filtering
 * tools found https://www.php.net/manual/en/book.filter.php
 *
 * You can add as many escape filters as you like and it will process them in index order.
 *
 * Example:
 * new FilterEscape([FILTER_SANITIZE_STRING, FILTER_SANITIZE_EMAIL]);
 *
 * @package Guerrilla\RequestFilters\Filters
 */
class FilterEscape implements FilterInterface
{
    /**
     * @var int[]
     */
    protected $filters;


    /**
     * FilterEscape constructor.
     * @param int[] $filters  (see https://www.php.net/manual/en/filter.filters.php)
     */
    public function __construct(array $filters = [FILTER_SANITIZE_STRING]){
        $this->filters = $filters;
    }

    /**
     * Append a filter
     * @param int $filter  (see https://www.php.net/manual/en/filter.filters.php)
     * @return $this
     */
    public function append(int $filter){

        $this->filters = array_push($this->filters, $filter);
        return $this;
    }

    /**
     * Overwrite and set all filters
     * @param int[] $filters  (see https://www.php.net/manual/en/filter.filters.php)
     * @return $this
     */
    public function set(array $filters = [FILTER_DEFAULT]){
        $this->filters = $filters;
        return $this;
    }

    function applyFilter(string $input): string
    {
        foreach($this->filters as $filter){
            $input = filter_var($input, $filter);
        }
        return $input;
    }


}
