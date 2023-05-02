<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rules\Password;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //
            'name' => ['nullable','string','max:225'],
            'email' => ['required','email','unique:users'],
            'phone' => ['nullable','string','max:13'],
            'altPhone' => ['nullable','string','max:13'],
            'password' =>['required','confirmed', Password::defaults()]
        ];
    }
}
