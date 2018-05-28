<?php

use Faker\Generator as Faker;

/* @var $factory \Illuminate\Database\Eloquent\Factory */
$factory->define(App\Site::class, function (Faker $faker) {
    return [
        'name' => $faker->words(3, true),
        'url' => $url = $faker->url,
        'root_uri' => $url.$faker->slug,
        'auth_type' => array_rand(array_flip(['jwt', 'oauth_1.0a', 'app_pwd'])),
        'auth_token' => $faker->sha256,
    ];
});
