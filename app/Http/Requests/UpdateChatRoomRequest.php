<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChatRoomRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'max_members' => 'required|integer|min:2',
        ];
    }
}
