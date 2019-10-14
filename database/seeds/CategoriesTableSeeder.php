<?php

use App\Media;
use Illuminate\Database\Seeder;
use App\MediaType;
use App\Category;

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
        foreach($types as $type)
        {
            Category::create(['media_type_id' => $type->id, 'name' => 'action']);
            Category::create(['media_type_id' => $type->id, 'name' => 'adventure']);
            Category::create(['media_type_id' => $type->id, 'name' => 'animation']);
            Category::create(['media_type_id' => $type->id, 'name' => 'comedy']);
            Category::create(['media_type_id' => $type->id, 'name' => 'crime']);
            Category::create(['media_type_id' => $type->id, 'name' => 'drama']);
            Category::create(['media_type_id' => $type->id, 'name' => 'fantasy']);
            Category::create(['media_type_id' => $type->id, 'name' => 'historical']);
            Category::create(['media_type_id' => $type->id, 'name' => 'horror']);
            Category::create(['media_type_id' => $type->id, 'name' => 'mystery']);
            Category::create(['media_type_id' => $type->id, 'name' => 'musical']);
            Category::create(['media_type_id' => $type->id, 'name' => 'sci-fi']);
            Category::create(['media_type_id' => $type->id, 'name' => 'thriller']);
        }
    }
}
