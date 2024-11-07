<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends BaseUuidModel
{
    public $timestamps = false;

    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'content',
        'attachment_url',
        'sent_at',
        'read',
    ];

    public function getRules(): array
    {
        return [
            'chat_room_id' => 'required|uuid|exists:chat_rooms,id',
            'sender_id' => 'required|uuid|exists:users,id',
            'content' => 'required|string',
            'attachment_url' => 'nullable|string',
            'sent_at' => 'required|date',
            'read' => 'required|boolean',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
