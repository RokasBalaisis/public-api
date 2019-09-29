<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


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
        DB::table('users')->insert(['username' => "Admin", 'email' => 'admin@admin.lt', 'password' => Hash::make('admin'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        // create 10 users using the user factory
        factory(App\User::class, 100)->create();
    }
}