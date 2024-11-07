<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends BaseUuidModel
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'contact_user_id',
    ];

    public function getRules(): array
    {
        return [
            'user_id' => 'required|uuid|exists:users,id',
            'contact_user_id' => 'required|uuid|exists:users,id',
        ];
    }

    public function contactUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contact_user_id');
    }
}
