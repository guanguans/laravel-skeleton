<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     */
    public function run(): void
    {
        DB::table('users')->delete();

        DB::table('users')->insert([
            [
                'email' => 'admin@admin.com',
                'email_verified_at' => '2021-11-18 18:10:18',
                'name' => 'admin',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'remember_token' => 'bkErVvxVox',
                'created_at' => '2021-11-18 18:10:18',
                'updated_at' => '2021-11-18 18:10:18',
            ],
        ]);
    }
}
