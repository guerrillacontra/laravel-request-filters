<?php

use Carbon\Carbon;
use Guerrilla\RequestFilters\Filters\FilterCapitalize;
use Guerrilla\RequestFilters\Filters\FilterDate;
use Guerrilla\RequestFilters\FilterParser;
use Guerrilla\RequestFilters\Filters\FilterNumeric;
use Guerrilla\RequestFilters\Filters\FilterStripTags;
use Guerrilla\RequestFilters\Filters\FilterToLower;
use Guerrilla\RequestFilters\Filters\FilterToUpper;
use Guerrilla\RequestFilters\Filters\FilterTrim;
use Guerrilla\RequestFilters\Filters\Sanitization\FilterSanitizeEmail;
use Guerrilla\RequestFilters\Filters\Sanitization\FilterSanitizeEncoded;
use Guerrilla\RequestFilters\Filters\Sanitization\FilterSantizeText;
use Guerrilla\RequestFilters\InputFilter;
use PHPUnit\Framework\TestCase;

class ParsingTest extends TestCase
{
    public function test_capitalize()
    {
        $filters = FilterParser::parseFilterString('capitalize');
        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterCapitalize::class, $filters[0]);
    }

    public function test_uppercase()
    {
        $filters = FilterParser::parseFilterString('uppercase');
        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterToUpper::class, $filters[0]);
    }

    public function test_lowercase()
    {
        $filters = FilterParser::parseFilterString('lowercase');
        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterToLower::class, $filters[0]);
    }


    public function test_trim()
    {
        $filters = FilterParser::parseFilterString('trim');
        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterTrim::class, $filters[0]);
    }

    public function test_strip()
    {
        $filters = FilterParser::parseFilterString('strip');
        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterStripTags::class, $filters[0]);
    }

    public function test_date()
    {
        $filters = FilterParser::parseFilterString('date');
        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterDate::class, $filters[0]);
    }

    public function test_number()
    {
        $filters = FilterParser::parseFilterString('number');
        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterNumeric::class, $filters[0]);
    }

    public function test_sanitize()
    {
        $filters = FilterParser::parseFilterString('sanitize');
        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterSantizeText::class, $filters[0]);
    }

    public function test_sanitize_email()
    {
        $filters = FilterParser::parseFilterString('email');
        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterSanitizeEmail::class, $filters[0]);
    }

    public function test_encode()
    {
        $filters = FilterParser::parseFilterString('encode');
        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterSanitizeEncoded::class, $filters[0]);
    }

    public function test_with_one_param(){

        $filters = FilterParser::parseFilterString("strip:<a><p>");

        $this->assertIsArray($filters);
        $this->assertCount(1, $filters);
        $this->assertInstanceOf(FilterStripTags::class, $filters[0]);
        $this->assertEquals("<a><p>", $filters[0]->allowable_tags);
    }


    public function test_combination()
    {
        $filters = FilterParser::parseFilterString('capitalize|uppercase');
        $this->assertIsArray($filters);
        $this->assertCount(2, $filters);
        $this->assertInstanceOf(FilterCapitalize::class, $filters[0]);
        $this->assertInstanceOf(FilterToUpper::class, $filters[1]);
    }

    public function test_combination_with_params()
    {
        $filters = FilterParser::parseFilterString("strip:<a><p>|date:d/m/Y");

        $this->assertIsArray($filters);
        $this->assertCount(2, $filters);
        $this->assertInstanceOf(FilterStripTags::class, $filters[0]);
        $this->assertEquals("<a><p>", $filters[0]->allowable_tags);
        $this->assertInstanceOf(FilterDate::class, $filters[1]);
        $this->assertEquals("d/m/Y", $filters[1]->format);
    }

    public function test_filter_array_of_string_literals()
    {
        $inputs = [
            'date1' => '07/11/1990',
            'date2' => '7th November 1990',
            'email' => ' <p>test@test.com</p><br> '
        ];

        $filters = [
            'date1' => 'date:d/m/Y',
            'date2' => 'date:jS M Y',
            'email' => 'strip:<br>|trim'
        ];

        $filtered_inputs = InputFilter::filterFromString($inputs, $filters, null);

        $this->assertEquals(Carbon::create(1990, 11, 7)->toDateString(), $filtered_inputs['date1']);
        $this->assertEquals(Carbon::create(1990, 11, 7)->toDateString(), $filtered_inputs['date2']);
        $this->assertEquals('test@test.com<br>', $filtered_inputs['email']);
    }
}
