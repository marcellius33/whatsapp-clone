<?php

namespace App\Models\Enums;

enum ChatRoomRole: string
{
    case Admin = 'admin';
    case Member = 'member';
}
