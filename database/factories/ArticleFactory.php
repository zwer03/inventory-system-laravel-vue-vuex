<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Article;
use Faker\Generator as Faker;
use App\User;
$factory->define(Article::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'title' => $faker->sentence,
        'body' => $faker->paragraph
    ];
});
