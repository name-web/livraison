<?php

namespace App\Http\Requests\MerchantPanel\Shops;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => ['required'],
            'contact_no' => ['required', 'regex:/^0[1-7][0-9]{8}$/'],
            'address' => ['required'],
            'status' => ['required', 'numeric'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'contact_no.regex' => __('merchantshops.contact_ivorian'),
        ];
    }
}
