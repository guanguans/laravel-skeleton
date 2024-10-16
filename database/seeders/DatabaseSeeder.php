<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\CanSeedOncePerDatabase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;
    use CanSeedOncePerDatabase;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        // $this->call(UsersTableSeeder::class);
        $this->callOncePerDatabase(UsersTableSeeder::class);
    }
}
