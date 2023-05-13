<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
            // 'user_id'=>['integer'],
            'name'=>['required','string'],
            'email'=>['required','email'],
            'phone'=>['required','string','max:13'],
            'car_id'=>['required','integer'],
            'seat_id'=>['required','integer'],
            'departure_city' => ['required'],
            'arrival_city' => ['required'],
            'kilometer'=>['integer','required'],
            // 'total_price'=>['integer'],
            'date'=>['string','required'],


        ];
    }
}
