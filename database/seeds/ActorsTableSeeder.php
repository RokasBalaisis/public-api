<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Actor;

class ActorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        for($i = 0; $i < 100; $i++)
            Role::create(['name' => $faker->unique()->firstName(), 'surname' => $faker->unique()->lastName(), 'born' => $faker->unique()->dateTime(), 'info' => $faker->unique()->paragraph(5, true)]);
    }
}