<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarsRequest extends FormRequest
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
            "name" => ["required","string"],
            "category_id" => ["required","integer"],
            "seat_no" => ["required","integer"],
            "brand" => ["required","string"],
            "model" => ["required","string"],
            "price" => ["required","integer"],
            "status" => ["integer"],

        ];
    }
}
