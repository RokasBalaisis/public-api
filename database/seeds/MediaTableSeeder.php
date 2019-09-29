<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
use App\Role;
use App\User;
use App\Media;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $amount = 100;
        for($i=0; $i < $amount; $i++)
        {
            Media::create(['name' => $faker->unique()->company, 'short_description' => $faker->unique()->paragraph(5, true), 'description' => $faker->unique()->text(200), 'trailer_url' => 'www.youtube.com/embed/meMc5MWvBLo']);
        }
    }
}