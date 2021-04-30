<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MeetingFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required | min:3',
            'agenda' => 'required | min:3',
            'client_custom_id' => 'required',
            'date' => 'required | date',
            'time' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Please enter a title.',
            'title.min' => 'Title must contain at least 3 characters.',

            'agenda.required' => 'Please give an agenda.',
            'agenda.min' => 'Agenda must contain at least 3 characters.',

            'client_custom_id.required' => 'Please enter the client ID.',

            'date.required' => 'Please select a date.',
            'date.date' => 'Please select a valid date.',

            'time.required' => 'Please select a time.'
        ];
    }
}
