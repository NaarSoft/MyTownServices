<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidayFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50',
            'day' => 'required|date_format:m/d/Y',
        ];
    }

    /**
     * Get the validation messages that apply to the rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Enter Holiday Name.',
            'name.max' => 'Holiday may be up to :max characters in length.',
            'day.required'  => 'Select Date.',
            'day.date_format' => 'Enter Date of Holiday in the following format m/d/Y (e.g., 01/20/2017 for 20th Dec 2017.',
        ];
    }
}
