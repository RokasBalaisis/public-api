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
        Category::create(['media_type' => 'movies', 'name' => 'action']);
        Category::create(['media_type' => 'series', 'name' => 'action']);
        Category::create(['media_type' => 'movies', 'name' => 'adventure']);
        Category::create(['media_type' => 'series', 'name' => 'adventure']);
        Category::create(['media_type' => 'movies', 'name' => 'animation']);
        Category::create(['media_type' => 'series', 'name' => 'animation']);
        Category::create(['media_type' => 'movies', 'name' => 'comedy']);
        Category::create(['media_type' => 'series', 'name' => 'comedy']);
        Category::create(['media_type' => 'movies', 'name' => 'crime']);
        Category::create(['media_type' => 'series', 'name' => 'crime']);
        Category::create(['media_type' => 'movies', 'name' => 'drama']);
        Category::create(['media_type' => 'series', 'name' => 'drama']);
        Category::create(['media_type' => 'movies', 'name' => 'fantasy']);
        Category::create(['media_type' => 'series', 'name' => 'fantasy']);
        Category::create(['media_type' => 'movies', 'name' => 'historical']);
        Category::create(['media_type' => 'series', 'name' => 'historical']);
        Category::create(['media_type' => 'movies', 'name' => 'horror']);
        Category::create(['media_type' => 'series', 'name' => 'horror']);
        Category::create(['media_type' => 'movies', 'name' => 'mystery']);
        Category::create(['media_type' => 'series', 'name' => 'mystery']);
        Category::create(['media_type' => 'movies', 'name' => 'musical']);
        Category::create(['media_type' => 'series', 'name' => 'musical']);
        Category::create(['media_type' => 'movies', 'name' => 'sci-fi']);
        Category::create(['media_type' => 'series', 'name' => 'sci-fi']);
        Category::create(['media_type' => 'movies', 'name' => 'thriller']);
        Category::create(['media_type' => 'series', 'name' => 'thriller']);
    }
}