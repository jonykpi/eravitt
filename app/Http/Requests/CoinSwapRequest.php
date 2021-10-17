<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoinSwapRequest extends FormRequest
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
            'from_coin_id' => 'required|exists:wallets,id',
            'to_coin_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric',
        ];
    }
    public function messages()
    {
        return [
            'from_coin_id.required' => __("From wallet id is required"),
            'to_coin_id.required' => __("To wallet id is required"),
        ];
    }
}
