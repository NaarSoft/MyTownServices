<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleFormRequest extends FormRequest
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
            'StartDate' => 'required|date_format:m/d/Y',
            'EndDate' => 'required|date_format:m/d/Y',
            // 'StartDate' => 'required|max:50',
            //'EndTimeHour' => 'required|date_format:m/d/Y',
            //'office_days' => 'required',
            'agency' => 'required',
            'agency_user' => 'required',
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
            'StartDate.required' => 'Enter Start Date.',
            'StartDate.date_format' => 'Enter Start Date in the following format m/d/Y (e.g., 01/20/2017 for 20th Dec 2017.',
            'EndDate.required' => 'Enter End Date.',
            'EndDate.date_format' => 'Enter End Date in the following format m/d/Y (e.g., 01/20/2017 for 20th Dec 2017.',
            'agency.required'  => 'Select Agency.',
            'agency_user.required'  => 'Select Agency User.',
        ];
    }
}
