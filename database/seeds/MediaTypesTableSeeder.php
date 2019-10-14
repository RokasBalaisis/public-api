<?php

use App\Media;
use Illuminate\Database\Seeder;
use App\MediaType;

class MediaTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MediaType::create(['name' => 'movies']);
        MediaType::create(['name' => 'series']);
    }
}