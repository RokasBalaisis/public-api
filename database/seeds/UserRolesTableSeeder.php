<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;

class UserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $adminIsSet = false;
        $users = DB::table('users')->get();
        $rolesIds = DB::table('roles')->pluck('id')->toArray();
        $data = array();
        foreach($users as $user)
        {
            if($adminIsSet == false)
            {
                array_push($data, (['user_id' => $user->id, 'role_id' => $rolesIds[0]]));
                $adminIsSet = true;
            }
            else
               array_push($data, (['user_id' => $user->id, 'role_id' => $faker->randomElement($rolesIds)]));
        }
        DB::table('user_role')->insert($data);
    }
}