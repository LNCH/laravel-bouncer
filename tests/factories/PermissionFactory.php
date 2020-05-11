<?php

use Faker\Generator as Faker;
use Lnch\LaravelBouncer\Models\Permission;

$factory->define(Permission::class, function (Faker $faker) {
    return [
        'key' => $faker->word,
        'group' => $faker->word,
        'label' => $faker->word,
        'description' => $faker->paragraph
    ];
});
