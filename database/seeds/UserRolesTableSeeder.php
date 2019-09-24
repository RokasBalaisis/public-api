<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
use App\Role;
use App\User;

class UserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $users = DB::table('users')->get();
        $rolesIds = DB::table('roles')->pluck('id')->toArray();
        foreach($users as $user)
        {
            DB::table('user_role')->insert(['user_id' => $user->id, 'role_id' => $faker->randomElement($rolesIds)]);
        }
    }
}