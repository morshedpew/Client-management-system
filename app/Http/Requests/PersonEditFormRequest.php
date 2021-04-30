<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonEditFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required | min:3',
            'designation' => 'required | min:3',
            'contact_number' => 'required | numeric',
            'email' => 'required | email'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter a name.',
            'name.min' => 'Name must contain at least 3 characters.',

            'designation.required' => 'Please enter a designation.',
            'designation.min' => 'Designation must contain at least 3 characters.',

            'contact_number.required' => 'Please enter a phone number.',
            'contact_number.numeric' => 'Only numbers are allowed.',

            'email.required' => 'Please enter an email.',
            'email.email' => 'Please enter a valid email.'
        ];
    }
}
