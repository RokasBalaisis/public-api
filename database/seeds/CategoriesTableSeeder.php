<?php

use Illuminate\Database\Seeder;
use App\MediaType;
use Carbon\Carbon;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = MediaType::all();
        $data = array();
        foreach($types as $type)
        {
            $current_timestamp = Carbon::now();
            array_push($data, (['media_type_id' => $type->id, 'name' => 'action', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'adventure', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'animation', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'comedy', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'crime', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'drama', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'fantasy', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'historical', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'horror', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'mystery', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'musical', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'sci-fi', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
            array_push($data, (['media_type_id' => $type->id, 'name' => 'thriller', 'created_at' => $current_timestamp, 'updated_at' => $current_timestamp]));
        }
        DB::table('categories')->insert($data);
    }
}
