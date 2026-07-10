<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DevUserSeeder::class,
            EmpresaParametroSeeder::class,
            SicoobSeeder::class,
            SOCSeeder::class
        ]);
    }
}
