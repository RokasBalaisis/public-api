<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Generator as Faker;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        // create initial admin account
        $data = array();
        array_push($data, (['username' => "Admin", 'email' => 'admin@admin.lt', 'password' => Hash::make('admin'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]));
        $amount = 100;
        for($i=0; $i < $amount; $i++)
        {
            $current_timestamp = Carbon::now();
            array_push($data, (['username' => $faker->unique()->userName, 'email' => $faker->unique()->email, 'password' => Hash::make('12345'), 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
        }
        DB::table('users')->insert($data);
    }
}