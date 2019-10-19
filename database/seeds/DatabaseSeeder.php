<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('UsersTableSeeder');
        $this->call('RolesTableSeeder');
        $this->call('UserRoleTableSeeder');
        $this->call('MediaTypesTableSeeder');
        $this->call('MediaTableSeeder');
        $this->call('ActorsTableSeeder');
        $this->call('CategoriesTableSeeder');
        $this-call('MediaActorsTableSeeder');
    }
}
