<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidModel;
use App\Models\Enums\ChatRoomRole;
use Illuminate\Validation\Rule;

class ChatRoomMember extends BaseUuidModel
{
    public $timestamps = false;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'role',
        'joined_at',
    ];

    public function getRules(): array
    {
        return [
            'chat_room_id' => 'required|uuid|exists:chat_rooms,id',
            'user_id' => 'required|uuid|exists:users,id',
            'role' => ['required', Rule::enum(ChatRoomRole::class)],
            'joined_at' => 'required|date',
        ];
    }
}
