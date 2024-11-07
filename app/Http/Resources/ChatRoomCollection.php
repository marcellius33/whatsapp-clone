<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatRoomCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $data = [];
        foreach ($this->collection as $chatRoom) {
            $data[] = new ChatRoomResource($chatRoom);
        }

        return $data;
    }
}
