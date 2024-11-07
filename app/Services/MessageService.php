<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MessageService
{
    public function storeMessage(array $input, User $user): void
    {
        if (!ChatRoomMember::where('chat_room_id', $input['chat_room_id'])->where('user_id', $user->id)->first()) {
            throw new BadRequestHttpException(__('error.not_a_member'));
        }

        $message = new Message($input);
        $message->sender_id = $user->id;
        $message->sent_at = Carbon::now();
        $message->read = false;
        $message->save();

        broadcast(new MessageSent(ChatRoom::find($input['chat_room_id'])));
    }
}
