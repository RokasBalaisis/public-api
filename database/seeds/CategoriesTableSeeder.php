<?php

use Illuminate\Database\Seeder;
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
        Category::create(['media_type' => 'movie', 'name' => 'action']);
        Category::create(['media_type' => 'series', 'name' => 'action']);
        Category::create(['media_type' => 'movie', 'name' => 'adventure']);
        Category::create(['media_type' => 'series', 'name' => 'adventure']);
        Category::create(['media_type' => 'movie', 'name' => 'animation']);
        Category::create(['media_type' => 'series', 'name' => 'animation']);
        Category::create(['media_type' => 'movie', 'name' => 'comedy']);
        Category::create(['media_type' => 'series', 'name' => 'comedy']);
        Category::create(['media_type' => 'movie', 'name' => 'crime']);
        Category::create(['media_type' => 'series', 'name' => 'crime']);
        Category::create(['media_type' => 'movie', 'name' => 'drama']);
        Category::create(['media_type' => 'series', 'name' => 'drama']);
        Category::create(['media_type' => 'movie', 'name' => 'fantasy']);
        Category::create(['media_type' => 'series', 'name' => 'fantasy']);
        Category::create(['media_type' => 'movie', 'name' => 'historical']);
        Category::create(['media_type' => 'series', 'name' => 'historical']);
        Category::create(['media_type' => 'movie', 'name' => 'horror']);
        Category::create(['media_type' => 'series', 'name' => 'horror']);
        Category::create(['media_type' => 'movie', 'name' => 'mystery']);
        Category::create(['media_type' => 'series', 'name' => 'mystery']);
        Category::create(['media_type' => 'movie', 'name' => 'musical']);
        Category::create(['media_type' => 'series', 'name' => 'musical']);
        Category::create(['media_type' => 'movie', 'name' => 'sci-fi']);
        Category::create(['media_type' => 'series', 'name' => 'sci-fi']);
        Category::create(['media_type' => 'movie', 'name' => 'thriller']);
        Category::create(['media_type' => 'series', 'name' => 'thriller']);
    }
}