<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'chat_room_id' => 'required|uuid|exists:chat_rooms,id',
            'content' => 'required|string',
            'attachment' => 'nullable|file',
        ];
    }
}
