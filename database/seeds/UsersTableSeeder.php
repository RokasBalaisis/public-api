<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create initial admin account
        DB::table('users')->insert(['username' => "Admin", 'email' => 'admin@admin.lt', 'password' => Hash::make('admin')]);
        // create 10 users using the user factory
        factory(App\User::class, 10)->create();
    }
}