<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use Todo\Model\Label;
use Illuminate\Support\Str;

$factory->define(Label::class, function (Faker $faker) {
    return [
        'name' => Str::random(10)
    ];
});
