<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
use App\Category;
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
        $categories = Category::all();
        $data = array();
        foreach($categories as $category)
            for($i = 0; $i < rand(1,5); $i++)
            {
                $current_timestamp = Carbon::now();
                array_push($data, (['category_id' => $category->id ,'name' => $faker->unique()->company, 'short_description' => $faker->unique()->paragraph(5, true), 'description' => $faker->unique()->text(200), 'trailer_url' => 'www.youtube.com/embed/meMc5MWvBLo', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            }
        DB::table('media')->insert($data);
    }
}