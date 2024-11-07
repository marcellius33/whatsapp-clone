<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatRoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'max_members' => $this->max_members,
            'is_group' => $this->is_group,
            'created_by' => new UserResource($this->creator),
        ];
    }
}
