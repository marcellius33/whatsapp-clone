<?php

use App\Models\ChatRoomMember;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat-room.{chatRoomId}', function (User $user, string $chatRoomId) {
    $chatRoomMember = ChatRoomMember::where('chat_room_id', $chatRoomId)
        ->where('user_id', $user->id)
        ->first();

    return !is_null($chatRoomMember);
});
