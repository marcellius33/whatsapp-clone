<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => [
                'required_without:phone_number',
                'string',
                'exists:users,username',
                function ($attribute, $value, $fail) {
                    if (auth()->user() && auth()->user()->username === $value) {
                        $fail('The ' . $attribute . ' must not be your own.');
                    }
                }
            ],
            'phone_number' => [
                'required_without:username',
                'string',
                'exists:users,phone_number',
                function ($attribute, $value, $fail) {
                    if (auth()->user() && auth()->user()->phone_number === $value) {
                        $fail('The ' . $attribute . ' must not be your own.');
                    }
                }
            ],
        ];
    }
}
