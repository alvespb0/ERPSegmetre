<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('DEV_USER_EMAIL', 'dev@segmetre.local')],
            [
                'name' => env('DEV_USER_NAME', 'Desenvolvedor'),
                'password' => Hash::make(env('DEV_USER_PASSWORD', 'password')),
                'tipo' => 'dev',
            ]
        );
    }
}
