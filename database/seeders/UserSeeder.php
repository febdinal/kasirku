<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@ventra.com'],
            [
                'name' => 'Admin VENTRA',
                'email' => 'admin@ventra.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $user->forceFill(['is_admin' => true])->save();

        if (! $user->store) {
            Store::createFor($user, 'VENTRA');
        }
    }
}
