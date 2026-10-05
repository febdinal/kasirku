<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('store:create {name : Nama toko} {email : Email login user pemilik} {password : Password user} {--owner= : Nama pemilik (default: nama toko)}')]
#[Description('Buat user baru beserta tokonya (1 user = 1 toko)')]
class CreateStore extends Command
{
    public function handle(): int
    {
        if (User::where('email', $this->argument('email'))->exists()) {
            $this->error('Email sudah terdaftar.');

            return self::FAILURE;
        }

        DB::transaction(function () {
            $user = User::create([
                'name' => $this->option('owner') ?: $this->argument('name'),
                'email' => $this->argument('email'),
                'password' => $this->argument('password'),
            ]);

            Store::createFor($user, $this->argument('name'));
        });

        $this->info('Toko "'.$this->argument('name').'" dibuat untuk '.$this->argument('email').'.');

        return self::SUCCESS;
    }
}
