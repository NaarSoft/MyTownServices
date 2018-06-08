<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingFormRequest extends FormRequest
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
            'office_days' => 'required|min:1',
//            'sunday' => 'required_without_all:monday,tuesday,wednesday,thursday,friday,saturday',
//            'monday' => 'required_without_all:sunday,tuesday,wednesday,thursday,friday,saturday',
//            'tuesday' => 'required_without_all:sunday,monday,wednesday,thursday,friday,saturday',
//            'wednesday' => 'required_without_all:sunday,monday,tuesday,thursday,friday,saturday',
//            'thursday' => 'required_without_all:sunday,monday,tuesday,wednesday,friday,saturday',
//            'friday' => 'required_without_all:sunday,monday,tuesday,wednesday,thursday,saturday',
//            'saturday' => 'required_without_all:sunday,monday,tuesday,wednesday,thursday,friday',
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
            'office_days.required'  => 'Select at least one.',
        ];
    }
}
