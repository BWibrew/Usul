<?php

use Faker\Generator as Faker;

/* @var $factory \Illuminate\Database\Eloquent\Factory */
$factory->define(App\Site::class, function (Faker $faker) {
    return [
        'name' => $faker->words(3, true),
        'url' => $url = $faker->url,
        'root_uri' => $url.$faker->slug().'/',
        'namespaces' => json_encode([]),
    ];
});
