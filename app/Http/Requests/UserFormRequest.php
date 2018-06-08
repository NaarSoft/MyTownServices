<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Response;

class UserFormRequest extends FormRequest
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
        //$regex = '/^((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%]).{8,20})$/';
        $regex = '/^((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\w\d\s]).{6,20})$/';

        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'max:50',
            'email'=> "required|email|min:6|max:50|unique:users",
            'contact_info'=> "max:100",
            'role_id'=> "required|not_in:0",
            'schedule_color'=> "required",
        ];

        // Validation agency_id only if role_id is 2 and agency_id is 0.
        if ($this->get('role_id') == 2 && $this->get('agency_id') == 0) {
            $rules["agency_id"] = ["not_in:0"];
        }

        if ($this->get('id') > 0) {
            if ($this->get('id') == Auth::user()->id) {
                $rules["role_id"] = [];
            }
            // forcing a unique rule to ignore a given id | https://laravel.com/docs/5.3/validation
            $rules["email"] = ["required", "email", Rule::unique("users", "email")->ignore($this->get('id'), "id")];
        }

        return $rules;
    }

    /**
     * Get the validation messages that apply to the rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'Enter First Name.',
            'first_name.max' => 'First Name may be up to :max characters in length.',
            'last_name.max' => 'Last Name may be up to :max characters in length.',
            'email.required'  => 'Enter Email.',
            'email.max'  => 'Email may be up to :max characters in length.',
            'email.email'  => 'Enter valid Email.',
            'email.unique'  => 'Email already exists. Please enter a different Email.',
            'contact_info.max'  => 'Contact Information may be up to :max characters in length.',
            'role_id.required' => 'Select Role.',
            'role_id.not_in'  => 'Select Role',
            'agency_id.not_in' => 'Select Agency',
            'schedule_color.required' => 'Select Color',
        ];
    }
}