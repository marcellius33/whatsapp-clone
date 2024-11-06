<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = new User([
            'name' => 'Jake',
            'email' => 'jake@gmail.com',
            'password' => Hash::make('jake123'),
            'email_verified_at' => Carbon::now(),
        ]);
        $user->save();
    }
}
