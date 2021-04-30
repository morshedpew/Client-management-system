<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientEditFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_name' => 'required | min:3',
            'company_name' => 'nullable | min:3',
            'conversion_date' => 'required | date | date_format:Y-m-d',

            'contact_number' => 'required | numeric',
            'email' => 'email | nullable',
            'address' => 'nullable | min:5',

            'comment_1' => 'nullable | min:3',
            'comment_2' => 'nullable | min:3',

            'source_id' => 'required',
            'service_id' => 'required',
            'assigned_person_id' => 'required',
            'lead_status_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'client_name.required' => 'Please enter a name.',
            'client_name.min' => 'Name must contain at least 3 characters.',

            'company_name.min' => 'Company name must contain at least 3 characters.',

            'conversion_date.required' => 'Please select a date.',
            'conversion_date.date' => 'Please select a valid date.',
            'conversion_date.date_format' => 'Date must follow this format - yyyy-mm-dd.',

            'contact_number.required' => 'Please enter a phone number.',
            'contact_number.numeric' => 'Only numbers are allowed.',

            'email.email' => 'Please enter a valid email address.',

            'address.min' => 'Address must contain at least 5 characters.',

            'comment_1.min' => 'Comment must contain at least 3 characters',
            'comment_2.min' => 'Comment must contain at least 3 characters',

            'source_id.required' => 'Please select a source.',
            'service_id.required' => 'Please select a service.',
            'assigned_person_id.required' => 'Please select an assigned person.',
            'lead_status_id.required' => 'Please select a lead status.'
        ];
    }
}
