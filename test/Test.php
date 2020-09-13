<?php

use Guerrilla\RequestFilters\Filters\FilterCapitalize;
use Guerrilla\RequestFilters\Filters\FilterEscape;
use Guerrilla\RequestFilters\Filters\FilterNumeric;
use Guerrilla\RequestFilters\Filters\FilterStripTags;
use Guerrilla\RequestFilters\Filters\FilterToLower;
use Guerrilla\RequestFilters\Filters\FilterToUpper;
use Guerrilla\RequestFilters\Filters\FilterTrim;
use Guerrilla\RequestFilters\RequestFiltering;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function test_capitalize(){

        $inputs = [
            'forename' => 'james',
        ];

        $filters =[
            'forename' => [new FilterCapitalize()]
        ];

        $filtered_inputs = RequestFiltering::filter($inputs, $filters);

        $this->assertEquals('James', $filtered_inputs['forename']);
    }

    public function test_to_lower(){

        $inputs = [
            'forename' => 'JaMes'
        ];

        $filters =[
            'forename' => [new FilterToLower()]
        ];

        $filtered_inputs = RequestFiltering::filter($inputs, $filters);

        $this->assertEquals('james', $filtered_inputs['forename']);
    }

    public function test_to_upper(){

        $inputs = [
            'forename' => 'JaMes'
        ];

        $filters =[
            'forename' => [new FilterToUpper()]
        ];

        $filtered_inputs = RequestFiltering::filter($inputs, $filters);

        $this->assertEquals('JAMES', $filtered_inputs['forename']);
    }

    public function test_trim(){

        $inputs = [
            'forename' => ' james '
        ];

        $filters =[
            'forename' => [new FilterTrim()]
        ];

        $filtered_inputs = RequestFiltering::filter($inputs, $filters);

        $this->assertEquals('james', $filtered_inputs['forename']);
    }

    public function test_extract_numerics(){

        $inputs = [
            'address' => '221b Baker Street, London, UK, NW1 6XE'
        ];

        $filters =[
            'address' => [new FilterNumeric()]
        ];

        $filtered_inputs = RequestFiltering::filter($inputs, $filters);

        $this->assertEquals('22116', $filtered_inputs['address']);
    }

    public function test_escape_xss(){

        $inputs = [
            'naughty' => "<script>alert('XSS')</script>"
        ];

        $filters =[
            'naughty' => [new FilterEscape([FILTER_SANITIZE_STRING])]
        ];

        $filtered_inputs = RequestFiltering::filter($inputs, $filters);

        $this->assertEquals('alert(&#39;XSS&#39;)', $filtered_inputs['naughty']);
    }

    public function test_strip_tags(){

        $inputs = [
            'tags' => '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>',
            'extra' =>'<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>'
        ];

        $filters =[
            'tags' => [new FilterStripTags()],
            'extra' => [new FilterStripTags('<p><a>')]
        ];

        $filtered_inputs = RequestFiltering::filter($inputs, $filters);

        $this->assertEquals('Test paragraph. Other text', $filtered_inputs['tags']);
        $this->assertEquals('<p>Test paragraph.</p> <a href="#fragment">Other text</a>', $filtered_inputs['extra']);
    }
}