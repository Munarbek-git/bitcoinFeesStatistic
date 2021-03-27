<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Statistic::class, function (Faker $faker) {
    return [
        'height' => random_int(1000, 2000),
        'fee' => random_int(1000, 2000)
    ];
});
