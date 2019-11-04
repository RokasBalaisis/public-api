
<?php

use Illuminate\Support\Facades\Hash;

/*
|-------------------------------------------------------------------
| Model Factories
|-------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories 
| give you a convenient way to create models for testing and seeding 
| your database. Just tell the factory how a default model should 
| look.
*/
$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'username'     => $faker->unique()->userName,
        'email'    => $faker->unique()->email,
        'password' => Hash::make('12345'),
    ];
});

$factory->define(App\Role::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name
    ];
});

$factory->define(App\Media::class, function (Faker\Generator $faker) {
    return [
        'category_id' => App\Category::all()->random()->id,
        'name' => $faker->unique()->name,
        'short_description' => $faker->unique()->paragraph(),
        'description' => $faker->unique()->paragraph(),
        'trailer_url' => 'www.youtube.com/embed/testtttt',
        'imdb_rating' => 5.5,
        'created_at' => null,
        'updated_at' => null
    ];
});
