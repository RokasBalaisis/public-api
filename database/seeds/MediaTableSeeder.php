<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
use App\Role;
use App\User;
use App\Media;
use App\MediaType;
use Carbon\Carbon;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $current_timestamp = Carbon::now();
        $media_types = MediaType::all();
        $data = array();
        $amount = 100;
        for($i=0; $i < $amount; $i++)
        {
            foreach($media_types as $media_type)
                array_push($data, (['media_type_id' => $media_type->id ,'name' => $faker->unique()->company, 'short_description' => $faker->unique()->paragraph(5, true), 'description' => $faker->unique()->text(200), 'trailer_url' => 'www.youtube.com/embed/meMc5MWvBLo', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
        }
        DB::table('media')->insert($data);
    }
}