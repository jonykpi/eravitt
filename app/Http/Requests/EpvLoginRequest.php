<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EpvLoginRequest extends FormRequest
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
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'email.required' => __("Email address can't empty"),
            'password.required' => __("Password can't empty"),
            'email.email' => __('Invalid email address.'),
        ];
    }
}
