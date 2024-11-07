<?php

namespace App\Services;

use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use App\Models\Enums\ChatRoomRole;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ChatRoomService
{
    public function storeChatRoom(array $input, User $user): void
    {
        $chatRoom = new ChatRoom($input);
        $chatRoom->is_group = true;
        $chatRoom->created_by = $user->id;
        $chatRoom->save();

        $chatRoomMember = new ChatRoomMember([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $user->id,
            'joined_at' => Carbon::now(),
            'role' => ChatRoomRole::Admin,
        ]);
        $chatRoomMember->save();
    }

    public function updateChatRoom(ChatRoom $chatRoom, array $input, User $user): void
    {
        $this->checkValidAction($chatRoom, $user);
        $chatRoom->fill($input);
        $chatRoom->save();
    }

    public function deleteChatRoom(ChatRoom $chatRoom, User $user): void
    {
        $this->checkValidAction($chatRoom, $user);
        ChatRoomMember::where('chat_room_id', $chatRoom->id)->delete();
        $chatRoom->delete();
    }

    public function actionChatRoom(ChatRoom $chatRoom, string $action, User $user): void
    {
        if ($action !== 'leave' && $action !== 'join') {
            throw new AccessDeniedHttpException(__('error.invalid_action'));
        }

        if ($action === 'leave' && $chatRoom->created_by === $user->id) {
            if (ChatRoomMember::where('chat_room_id', $chatRoom->id)->count() > 1) {
                throw new AccessDeniedHttpException(__('error.invalid_action'));
            }
            ChatRoomMember::where('chat_room_id', $chatRoom->id)->delete();
            $chatRoom->delete();
            return;
        }

        if ($action === 'join') {
            if (ChatRoomMember::where('chat_room_id', $chatRoom->id)->where('user_id', $user->id)->first()) {
                throw new BadRequestHttpException(__('error.already_join_chat_room'));
            }

            if (ChatRoomMember::where('chat_room_id', $chatRoom->id)->count() >= $chatRoom->max_members) {
                throw new BadRequestHttpException(__('error.room_is_full'));
            }

            $chatRoomMember = new ChatRoomMember([
                'chat_room_id' => $chatRoom->id,
                'user_id' => $user->id,
                'joined_at' => Carbon::now(),
                'role' => ChatRoomRole::Member,
            ]);
            $chatRoomMember->save();
        } else {
            ChatRoomMember::where('chat_room_id', $chatRoom->id)
                ->where('user_id', $user->id)
                ->delete();
        }
    }

    private function checkValidAction(ChatRoom $chatRoom, User $user): void
    {
        if ($chatRoom->created_by != $user->id) {
            throw new AccessDeniedHttpException(__('error.invalid_action'));
        }
    }
}
