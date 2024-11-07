<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MessageCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $data = [];
        foreach ($this->collection as $message) {
            $data[] = [
                'sender' => new UserResource($message->sender),
                'content' => $message->content,
                'sent_at' => $message->sent_at,
            ];
        }

        return $data;
    }
}
