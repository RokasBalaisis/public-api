<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $data = array();
        $amount = 100;
        for($i=0; $i < $amount; $i++)
        {
            $current_timestamp = Carbon::now();
            array_push($data, (['name' => $faker->unique()->firstName(), 'surname' => $faker->unique()->lastName(), 'born' => $faker->unique()->dateTime(), 'info' => $faker->unique()->paragraph(5, true), 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
        }
        DB::table('actors')->insert($data);
    }
}