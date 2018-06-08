<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Response;

class ChangePasswordFormRequest extends FormRequest
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
        $regex = '/^((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\w\d\s]).{6,20})$/';

        $rules = [
            'old_password'=> "required",
            'password'=> "required|min:8|max:20|regex:".$regex,
            'password_confirmation'=> "required|min:8|max:20|same:password",
        ];

        if ($this->get('id') > 0) {
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
            'old_password.required'  => 'Enter Old Password.',
            'password.required'  => 'Enter New Password.',
            'password.min'  => 'New Password must be at least :min characters and may be up to 20 characters in length.',
            'password.max'  => 'New Password must be at least 8 characters and may be up to :max characters in length.',
            'password.regex'  => 'New Password must contain at least 1 special character, 1 small letter, 1 capital letter and 1 numeric character.',
            'password_confirmation.required'  => 'Enter Confirm Password.',
            'password_confirmation.same'  => 'New Password and Confirm Password do not match.',
        ];
    }
}