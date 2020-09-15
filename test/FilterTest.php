<?php

use Carbon\Carbon;
use Guerrilla\RequestFilters\Filters\FilterCapitalize;
use Guerrilla\RequestFilters\Filters\FilterDate;
use Guerrilla\RequestFilters\Filters\Sanitization\FilterSanitize;
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

class FilterTest extends TestCase
{
    public function test_capitalize()
    {

        $inputs = [
            'forename' => 'james',
        ];

        $filters = [
            'forename' => [new FilterCapitalize()]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals('James', $filtered_inputs['forename']);
    }

    public function test_to_lower()
    {

        $inputs = [
            'forename' => 'JaMes'
        ];

        $filters = [
            'forename' => [new FilterToLower()]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals('james', $filtered_inputs['forename']);
    }

    public function test_to_upper()
    {

        $inputs = [
            'forename' => 'JaMes'
        ];

        $filters = [
            'forename' => [new FilterToUpper()]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals('JAMES', $filtered_inputs['forename']);
    }

    public function test_trim()
    {

        $inputs = [
            'forename' => ' james '
        ];

        $filters = [
            'forename' => [new FilterTrim()]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals('james', $filtered_inputs['forename']);
    }

    public function test_extract_numerics()
    {

        $inputs = [
            'address' => '221b Baker Street, London, UK, NW1 6XE'
        ];

        $filters = [
            'address' => [new FilterNumeric()]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals('22116', $filtered_inputs['address']);
    }

    public function test_sanitize_xss()
    {

        $inputs = [
            'naughty' => "<script>alert('XSS')</script>"
        ];

        $filters = [
            'naughty' => [new FilterSanitize([FILTER_SANITIZE_STRING])]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals('alert(&#39;XSS&#39;)', $filtered_inputs['naughty']);
    }

    public function test_sanitize_email()
    {

        $inputs = [
            'email' => "test@test.com<script>alert('oops')</script>"
        ];

        $filters = [
            'email' => [new FilterSanitizeEmail()]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals("test@test.comscriptalert'oops'script", $filtered_inputs['email']);
    }

    public function test_sanitize_text()
    {

        $inputs = [
            'naughty' => "<script>alert('XSS')</script>"
        ];

        $filters = [
            'naughty' => [new FilterSantizeText()]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals('alert(&#39;XSS&#39;)', $filtered_inputs['naughty']);
    }

    public function test_sanitize_encoded()
    {

        $inputs = [
            'encoded' => "http://www.google.com"
        ];

        $filters = [
            'encoded' => [new FilterSanitizeEncoded()]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals('http%3A%2F%2Fwww.google.com', $filtered_inputs['encoded']);
    }

    public function test_strip_tags()
    {

        $inputs = [
            'tags' => '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>',
            'extra' => '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>'
        ];

        $filters = [
            'tags' => [new FilterStripTags()],
            'extra' => [new FilterStripTags('<p><a>')]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals('Test paragraph. Other text', $filtered_inputs['tags']);
        $this->assertEquals('<p>Test paragraph.</p> <a href="#fragment">Other text</a>', $filtered_inputs['extra']);
    }


    public function test_date()
    {
        $inputs = [
            'date1' => '07/11/1990',
            'date2' => '7th November 1990'
        ];

        $filters = [
            'date1' => [new FilterDate('d/m/Y')],
            'date2' => [new FilterDate('jS M Y')]
        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals(Carbon::create(1990, 11, 7)->toDateString(), $filtered_inputs['date1']);
        $this->assertEquals(Carbon::create(1990, 11, 7)->toDateString(), $filtered_inputs['date2']);
    }


    public function test_nested(){
        $inputs = [
            'company'=>'test inc',
            'meta'=>[
                'age'=>'a22',
                'extra'=>[
                    'code'=>'a12345a'
                ]
            ],
            'employees'=>[
                [
                    'name'=>' james',
                    'role'=>'laravel'
                ],
                [
                    'name'=>'claire ',
                    'role'=>'dark arts '
                ]
            ]
        ];

        $filters = [
            'company' => [new FilterToUpper()],
            'meta.age'=> [new FilterNumeric()],
            'meta.extra.code' => [new FilterNumeric()],
            'employees.*.name' => [new FilterToUpper(), new FilterTrim()],
            'employees.*.role' => [new FilterToUpper(), new FilterTrim()],
            'employees.*.doesntexist'=>[new FilterTrim()]//ensures optional fields work

        ];

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->assertEquals('TEST INC', $filtered_inputs['company']);
        $this->assertEquals('22', $filtered_inputs['meta']['age']);
        $this->assertEquals('12345', $filtered_inputs['meta']['extra']['code']);
        $this->assertEquals('JAMES', $filtered_inputs['employees'][0]['name']);
        $this->assertEquals('CLAIRE', $filtered_inputs['employees'][1]['name']);
        $this->assertEquals('LARAVEL', $filtered_inputs['employees'][0]['role']);
        $this->assertEquals('DARK ARTS', $filtered_inputs['employees'][1]['role']);
    }



}
