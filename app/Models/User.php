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
        'username',
        'phone_number',
        'name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getRules(): array
    {
        return [
            'username' => 'required|string|unique:users,username,' . $this->id,
            'phone_number' => 'required|string|unique:users,phone_number,' . $this->id,
            'password'  => 'required|string|min:6',
            'name'      => 'required|string',
        ];
    }

    public function findForPassport(string $username): User
    {
        return $this->where('username', $username)
            ->orWhere('phone_number', $username)
            ->first();
    }
}
