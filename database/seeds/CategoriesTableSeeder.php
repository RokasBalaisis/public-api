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
            Category::create(['media_type' => $type->name, 'name' => 'action']);
            Category::create(['media_type' => $type->name, 'name' => 'adventure']);
            Category::create(['media_type' => $type->name, 'name' => 'animation']);
            Category::create(['media_type' => $type->name, 'name' => 'comedy']);
            Category::create(['media_type' => $type->name, 'name' => 'crime']);
            Category::create(['media_type' => $type->name, 'name' => 'drama']);
            Category::create(['media_type' => $type->name, 'name' => 'fantasy']);
            Category::create(['media_type' => $type->name, 'name' => 'historical']);
            Category::create(['media_type' => $type->name, 'name' => 'horror']);
            Category::create(['media_type' => $type->name, 'name' => 'mystery']);
            Category::create(['media_type' => $type->name, 'name' => 'musical']);
            Category::create(['media_type' => $type->name, 'name' => 'sci-fi']);
            Category::create(['media_type' => $type->name, 'name' => 'thriller']);
        }
    }
}
