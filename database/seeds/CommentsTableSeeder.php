<?php

use App\Media;
use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
use Carbon\Carbon;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $media = Media::all();
        $user_ids = User::all()->pluck('id');
        $data = array();
        foreach($media as $entry)
        {
            for($i = 0; $i < rand(1,5); $i++)
            {
                $current_timestamp = Carbon::now();
                array_push($data, (['media_id' => $entry->id, 'user_id' => $faker->randomElement($user_ids), 'text' => $faker->text(200), 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            }
                
        }
        DB::table('comments')->insert($data);
    }
}