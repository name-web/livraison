<?php

namespace App\Http\Requests\Merchant;

use App\Models\Backend\Merchant;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        $user = Merchant::findOrFail($this->id);
        $userID = $user->user_id;

        return [
            'name' => [
                'required',
                'string',
                'max:191',
            ],

            'business_name' => [
                'required',
                'string',
                'unique:merchants,business_name,'.$this->id,
            ],

            'mobile' => [
                'required',
                'regex:/^0[157][0-9]{8}$/',
                'unique:users,mobile,'.$userID,
            ],

            'hub' => [
                'required',
                'numeric',
            ],

            'status' => [
                'required',
                'numeric',
            ],

            'password' => [
                'nullable',
                'min:6',
            ],

            'address' => [
                'required',
                'string',
                'max:191',
            ],

            'payment_period' => [
                'numeric',
            ],

            'nid' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:10240',
            ],

            'trade_license' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:10240',
            ],

            'image_id' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:10240',
            ],
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'mobile.required' => __('merchant.mobile_required'),
            'mobile.regex' => __('merchant.mobile_regex'),
            'mobile.unique' => __('merchant.mobile_unique'),
        ];
    }
}
