<?php

use App\Media;
use Illuminate\Database\Seeder;
use App\MediaType;
use App\Actor;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;

class MediaActorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $media = Media::all();
        $actor_ids = Actor::all()->pluck('id');
        $data = array();
        foreach($media as $entry)
        {
            for($i = 0; $i < rand(1,5); $i++)
                array_push($data, (['media_id' => $entry->id, 'actor_id' => $faker->randomElement($actor_ids)]));
        }
        DB::table('media_actors')->insert($data);
    }
}