<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);

        // Data contoh dibuat atas nama toko milik admin.
        Auth::login(User::where('email', 'admin@ventra.com')->firstOrFail());

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        Auth::logout();
    }
}
