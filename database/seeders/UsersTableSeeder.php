<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
    {
        DB::table('users')->insert([
        [
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456')
        ],
        [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => bcrypt('123456')
        ]
        ]);
    }
}
