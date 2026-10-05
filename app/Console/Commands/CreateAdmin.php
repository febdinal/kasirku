<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('admin:create {name : Nama admin} {email : Email login} {password : Password}')]
#[Description('Buat akun admin (tanpa toko) untuk mengelola user dan toko')]
class CreateAdmin extends Command
{
    public function handle(): int
    {
        if (User::where('email', $this->argument('email'))->exists()) {
            $this->error('Email sudah terdaftar.');

            return self::FAILURE;
        }

        $admin = User::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => $this->argument('password'),
        ]);
        $admin->forceFill(['is_admin' => true])->save();

        $this->info('Admin '.$this->argument('email').' dibuat.');

        return self::SUCCESS;
    }
}
