<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatRoom extends BaseUuidModel
{
    protected $fillable = [
        'name',
        'max_members',
        'is_group',
        'created_by',
    ];

    public function getRules(): array
    {
        return [
            'name' => 'required|string',
            'max_members' => 'required|integer|min:2',
            'is_group' => 'required|boolean',
            'created_by' => 'required|uuid|exists:users,id',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
