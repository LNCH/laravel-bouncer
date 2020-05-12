<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Lnch\LaravelBouncer\Tests\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'email_verified_at' => time(),
        'password' => Hash::make('password'),
    ];
});
