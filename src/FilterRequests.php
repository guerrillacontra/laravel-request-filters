<?php

namespace Guerrilla\RequestFilters;

/**
 * A trait that can be applied to any Laravel Request/FormRequest
 * to enable custom input filtering rules.
 *
 * A request that is validated:
 * $clean_input = $request->validated();
 *
 * ^ Will also have its input filtered using the rule provided in the 'filters()'
 * associative array.
 *
 * @package Guerrilla\RequestFilters
 */
trait FilterRequests {

    /**
     * The filters you would like to use on this Request.
     * @return array[] An associative array of filters based on the inputs.
     * [
     *  'input_key' => [new FilterEscape(), new FilterTrim(), ...etc]
     * ]
     */
    public function filters(): array
    {
        return [
            'default'=>[
                //FilterInterface instance....
            ]
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $inputs = $this->input();
        $filters = $this->filters();

        $filtered_inputs = InputFilter::filter($inputs, $filters);

        $this->merge($filtered_inputs);

        parent::prepareForValidation();
    }

}
