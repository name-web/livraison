<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\Request;

class SignUpRequest extends FormRequest
{
    use ApiReturnFormatTrait;
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
            'business_name'     => ['required','string','unique:merchants'],
            'full_name'         => ['required','string','max:191'],
            'address'           => ['required','string','max:191'],
            'mobile'            => ['required','regex:/^0[1-7][0-9]{8}$/','unique:users', function ($attribute, $value, $fail) {
                $normalized = '+225' . $value;
                if (\App\Models\User::where('mobile', $normalized)->exists()) {
                    $fail(__('validation.unique', ['attribute' => $attribute]));
                }
            }],
            'password'          => ['required','min:6']
        ];
    }
}
