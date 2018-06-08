<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Response;

class AgencyFormRequest extends FormRequest
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
        $rules =  [
            'name' => 'required|max:100|unique:agency',
            'address' => 'required|max:500',
            'contact_info'=> "max:100",
            'website'=> "max:100",
            'service_id' => 'required|not_in:0',
            'service_name' => 'required|max:50',
            'image' => 'mimes:jpeg,jpg,png|max:1024',
        ];

        if ($this->get('id') > 0) {
            // forcing a unique rule to ignore a given id | https://laravel.com/docs/5.3/validation
            $rules["name"] = ["required", "max:100", Rule::unique("agency", "name")->ignore($this->get('id'), "id")];
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
            'name.required' => 'Enter Name.',
            'name.max' => 'Name may be up to :max characters in length.',
            'name.unique'  => 'Name already exists. Please enter a different Name.',
            'address.required' => 'Enter Address.',
            'address.max' => 'Address may be up to :max characters in length.',
            'contact_info.max' => 'Contact Information may be up to :max characters in length.',
            'website.max' => 'Website may be up to :max characters in length.',
            'service_id.required' => 'Select Service.',
            'service_id.not_in' => 'Select Service.',
            'service_name.required' => 'Enter Service Name.',
            'service_name.max' => 'Service Name may be up to :max characters in length.',
            'image.mimes' => 'Logo must be a file of type: jpeg, jpg, png',
            'image.max' => 'Logo may not be greater than 1 MB.',
        ];
    }
}
