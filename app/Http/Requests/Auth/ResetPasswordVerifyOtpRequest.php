<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiBaseRequestTrait;
use App\Rules\Auth\ResetPasswordOtpVerifyRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordVerifyOtpRequest extends FormRequest
{
    use ApiBaseRequestTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'otp' => [
                'required',
                'digits:6',
                new ResetPasswordOtpVerifyRule($this->email),
            ],
        ];
    }


    //     protected function failedValidation(Validator $validator)
    //     {
    //     throw new HttpResponseException(response()->json([
    //         'success' => false,
    //         'error' => $validator->errors()->all(),
    //     ], 422));
    // }
}
