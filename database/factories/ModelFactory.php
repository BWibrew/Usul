<?php

use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Site::class, function (Faker $faker) {
    return [
        'name' => $faker->words(3, true),
        'url' => $faker->url(),
        'namespaces' => json_encode([]),
    ];
});
