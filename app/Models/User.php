<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidUser;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends BaseUuidUser
{
    use Notifiable;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setEmailAttribute(string $value): void
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function getRules(): array
    {
        return [
            'email'     => 'required|email|unique:users,email,' . $this->id,
            'password'  => 'required|string|min:6',
            'name'      => 'required|string',
        ];
    }
}
